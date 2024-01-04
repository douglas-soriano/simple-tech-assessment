<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFundRequest;
use App\Http\Resources\FundResource;
use App\Helpers\ApiResponse;
use App\Models\FundManagerFund;
use App\Models\Fund;
use App\Models\FundAlias;

class FundController extends Controller
{

    # GET :: /api/funds -  Display a list of funds optionally filtered by name, fund manager and/or year.
    public function index()
    {
        try {

            $query = Fund::query();

            # Filter :: by name
            if (request()->filled('name')) {
                $query->where('name', 'LIKE', '%' . request()->input('name') . '%');
            }

            # Filter :: by manager's name
            if (request()->filled('fund_manager_name')) {
                $query->whereHas('fundManager', function ($subquery) {
                    $subquery->where('name', 'LIKE', '%' . request()->input('fund_manager_name') . '%');
                });
            }

            # Filter :: by manager's id
            if (request()->filled('fund_manager_id')) {
                $query->whereHas('fundManager', function ($subquery) {
                    $subquery->where('id', request()->input('fund_manager_id'));
                });
            }

            # Filter :: by start date
            if (request()->filled('start_year')) {
                $query->whereDate('start_year', request()->input('start_year'));
            }

            $funds = $query->with('fundManager.company', 'aliases')->simplePaginate(10);
            $formatted_funds = FundResource::collection($funds);

            return response()->json(ApiResponse::success($funds));

        } catch (\Exception $e) {
            return response()->json(ApiResponse::error('An unexpected error occurred.', 500));
        }
    }

    # GET :: /api/funds/{fund_id} - Get the fund's data.
    public function show($fund_id)
    {
        try {

            $fund = Fund::findOrFail($fund_id);
            return response()->json(ApiResponse::success(new FundResource($fund)));

        } catch (ModelNotFoundException $e) {
            return response()->json(ApiResponse::error('Fund not found.', Response::HTTP_NOT_FOUND));
        } catch (\Exception $e) {
            return response()->json(ApiResponse::error('An unexpected error occurred.', Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    # PUT :: /api/funds/{fund_id} - Update a Fund and all related attributes.
    public function update(UpdateFundRequest $request, $fund_id)
    {
        try {

            $fund = Fund::findOrFail($fund_id);
            $fund->fill($request->only(['name', 'start_year']));

            # Update manager
            $fund->updateManager($request->input('fund_manager_id'));

            # Update aliases
            if ($request->has('aliases')) {
                $fund->updateAliases($request->input('aliases'));
            }

            # Save changes
            $fund->save();

            # Check for duplicates
            $fund->checkAndTriggerDuplicateWarning();

            return response()->json(ApiResponse::success(new FundResource($fund->fresh())));

        } catch (ModelNotFoundException $e) {
            return response()->json(ApiResponse::error('Fund not found.', Response::HTTP_NOT_FOUND));
        } catch (ValidationException $e) {
            return response()->json(ApiResponse::error('Database error.', Response::HTTP_UNPROCESSABLE_ENTITY));
        } catch (\Exception $e) {
            return response()->json(ApiResponse::error('An unexpected error occurred.', Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    # GET :: /api/funds/potential-duplicates - List all potentially duplicated funds.
    public function getPotentialDuplicates()
    {
        # Query for possible duplicates
        $potential_duplicates = Fund::selectRaw(
                'GROUP_CONCAT(funds.id) as duplicate_ids,
                funds.name as fund_name,
                funds_managers.name as fund_manager,
                GROUP_CONCAT(funds_aliases.title) as aliases'
            )

            # Query dependencies
            ->join('fund_manager_fund', 'funds.id', '=', 'fund_manager_fund.fund_id')
            ->join('funds_managers', 'fund_manager_fund.fund_manager_id', '=', 'funds_managers.id')
            ->leftJoin('funds_aliases', 'funds.id', '=', 'funds_aliases.fund_id')
            ->whereExists(function ($query) {

                # Duplicated logic
                $query->select(\DB::raw(1))
                    ->from('funds as f')
                    ->join('fund_manager_fund', 'f.id', '=', 'fund_manager_fund.fund_id')
                    ->leftJoin('funds_aliases', 'f.id', '=', 'funds_aliases.fund_id')
                    ->whereRaw('f.id <> funds.id')
                    # Search with the same manager
                    ->whereRaw('fund_manager_fund.fund_manager_id = funds_managers.id')
                    ->where(function ($q) {
                        # Search by duplicated name
                        $q->whereRaw('f.name = funds.name');
                        # Search by duplicated aliases
                        $q->orWhereExists(function ($subquery) {
                            $subquery->select(\DB::raw(1))->from('funds_aliases')->whereRaw('funds_aliases.fund_id = f.id')->whereRaw('funds_aliases.title = funds.name');
                        });
                    });

            })
            ->groupBy('funds.name', 'funds_managers.name')
            ->orderBy('funds.id')
            ->get();

        # Format the results
        $formatted_results = $potential_duplicates->map(function ($result) {
            return [
                'duplicate_ids' => explode(',', $result->duplicate_ids),
                'fund_name' => $result->fund_name,
                'fund_manager' => $result->fund_manager,
                'aliases' => explode(',', $result->aliases),
            ];
        });

        return response()->json(ApiResponse::success($formatted_results));
    }

}
