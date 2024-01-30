<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFundRequest;
use App\Http\Resources\FundResource;
use App\Models\Fund;
use App\Services\FundService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class FundController extends Controller
{
    private FundService $fund_service;

    public function __construct(FundService $fund_service)
    {
        $this->fund_service = $fund_service;
    }

    # GET :: /api/funds -  Display a list of funds optionally filtered by name, fund manager and/or year.
    public function index()
    {
        try {
            $filters = request()->only('name', 'fund_manager_name', 'fund_manager_id', 'start_year');
            $funds = $this->fund_service->getFunds($filters);
            $formatted_funds = FundResource::collection($funds)->response()->getData();
            return ApiResponse::success($formatted_funds);
        } catch (Exception $e) {
            return ApiResponse::error('An unexpected error occurred.');
        }
    }

    # GET :: /api/funds/{fund_id} - Get the fund's data.
    public function show($fund_id)
    {
        try {
            $fund = Fund::findOrFail($fund_id);
            return ApiResponse::success(new FundResource($fund));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Fund not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::error('An unexpected error occurred.');
        }
    }

    # PUT :: /api/funds/{fund_id} - Update a Fund and all related attributes.
    public function update(UpdateFundRequest $request, $fund_id)
    {
        try {
            $fund = Fund::findOrFail($fund_id);
            $data = $request->only('name', 'start_year');
            $this->fund_service->updateFund($fund, $data);
            return ApiResponse::success(new FundResource($fund->fresh()));
        } catch (ValidationException $e) {
            return ApiResponse::error($e->errors());
        } catch (Exception $e) {
            return ApiResponse::error('An unexpected error occurred.');
        }
    }

    # GET :: /api/funds/potential-duplicates - List all potentially duplicated funds.
    public function getPotentialDuplicates()
    {
        try {
            $potential_duplicates = $this->fund_service->getPotentialDuplicates();
            $formatted_results = $potential_duplicates->map(function ($result) {
                return [
                    'duplicate_ids' => explode(',', $result->duplicate_ids),
                    'fund_name' => $result->fund_name,
                    'fund_manager' => $result->fund_manager,
                    'aliases' => explode(',', $result->aliases),
                ];
            });
            return ApiResponse::success($formatted_results);
        } catch (Exception $e) {
            return ApiResponse::error('An unexpected error occurred.');
        }
    }

}
