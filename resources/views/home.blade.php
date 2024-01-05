@extends('master')

@section('content')
<div id="app" class="container d-flex align-items-center justify-content-center">
    <div>

        <div class="logo mb-3">
            <img src="{{ asset('assets/images/logo.svg') }}" alt="Canoe">
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <img width="16" class="me-1" src="{{ asset('assets/images/icon-filter.svg') }}" alt="Filter Icon" style="margin-top:-2px">
                    Filter funds
                </div>
            </div>
            <div class="card-body p-0">
                <div class="search-wrapper px-4 py-3">
                    <div class="search-area">
                        <input type="text" class="search-form" placeholder="Filter funds by name..." v-model="search_term" v-on:keyup="searchTimeout">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.967 15.91">
                            <path d="M15.341,15.909c-.227,0-.34,0-.454-.114l-4.712-4.71A6.226,6.226,0,0,1,1.846,1.847a6.172,6.172,0,0,1,8.807,0,6.166,6.166,0,0,1,.38,8.392L15.8,15a.548.548,0,0,1,0,.795C15.682,15.909,15.568,15.909,15.341,15.909ZM6.25,1.137a5.114,5.114,0,1,0,5.114,5.114A5.12,5.12,0,0,0,6.25,1.137Z" fill="#d4d3d3" />
                        </svg>
                    </div>
                </div>

                <template v-if="is_loading">
                    <div class="py-4">
                        @include("components.loader")
                    </div>
                </template>

                <template v-else>
                    <ul v-if="!has_error" class="funds-list">

                        <template v-if="funds.data && funds.data.length">
                            <li v-for="fund in funds.data" class="px-4 py-3">
                                <div class="row">
                                    <div class="col">
                                        <h2 class="fund-name" v-text="fund.name" title="Fund Name"></h2>
                                    </div>
                                    <div class="col text-end" title="Start Year">
                                        <p class="year" v-text="fund.start_year"></p>
                                    </div>
                                </div>
                                <div v-if="fund.aliases" class="aliases mb-1" title="Aliases">
                                    <span v-for="alias in fund.aliases" class="alias" v-text="alias"></span>
                                </div>
                                <div class="manager text-muted fst-italic" title="Manager @ Company">
                                    <img width="12" src="{{ asset('assets/images/icon-manager.svg') }}" alt="left" style="margin-top:-2px">
                                    <span v-text="fund.fund_manager ? limitText(fund.fund_manager.name + ' @ ' + fund.fund_manager.company_name, 40) : '(Fund without manager)'"></span>
                                </div>
                            </li>
                        </template>

                        <template v-else>
                            <li class="p-3">
                                <div class="alert alert-info text-center mb-0" style="font-size:13px;"><b>No funds to show.</b><br><br>Use "php artisan dummy:populate 1000" command to populate the database with dummy data.</div>
                            </li>
                        </template>

                    </ul>
                    <div class="p-3" v-else>
                        <div class="alert alert-danger text-center mb-0" v-text="has_error"></div>
                    </div>
                </template>

            </div>
            <div class="card-footer px-4">
                <div class="d-grid">
                    <div class="input-group">
                        <button type="button" class="btn btn-teal px-4" v-on:click="prevFundsPage" v-bind:class="{ 'disabled': !funds.prev_page_url }">
                            <img width="16" src="{{ asset('assets/images/icon-arrow-left.svg') }}" alt="left" style="margin-top:-2px">
                        </button>
                        <input type="text" class="form-control text-center bg-white border-0" v-model="funds.current_page" readonly disabled>
                        <button type="button" class="btn btn-teal px-4" v-on:click="nextFundsPage" v-bind:class="{ 'disabled': !funds.next_page_url }">
                            <img width="16" src="{{ asset('assets/images/icon-arrow-right.svg') }}" alt="right" style="margin-top:-2px">
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="links text-center mt-3">
            <a target="_blank" href="https://miro.com/app/board/uXjVN9_xdHM=/?share_link_id=659916370064">ER Diagram</a>
            |
            <a target="_blank" href="https://bitbucket.org/douglas_soriano/canoe-tech-assessment">Repository / Documentation</a>
        </div>

    </div>
</div>

@endsection

@section('modals')

@endsection

@section('js')
<script>
var app = new Vue({
    el: '#app',
    data: {
        has_error: false,
        is_loading: true,
        search_timer: null,
        search_term: null,
        funds: {},
        current_page: 1
    },
    mounted: function () {
        this.getFunds();
    },
    methods: {

        // GET :: Search for funds on API.
        getFunds () {
            var self = this;

            // Initial
            self.has_error = false;
            self.is_loading = true;

            // Params
            let params = {
                name: self.search_term,
                page: self.current_page
            };

            // Request
            axios.get(API.apiUrl + '/funds', { params: params }).then(function (response) {
                self.is_loading = false;
                if (response.data.success) {
                    var data = response.data.response;
                    self.funds = data;
                } else {
                    self.has_error = response.data.error + ' (' + response.data.status_code + ')';
                }
            });

        },

        // SEARCH :: Apply a timeout so it wont make so many requests.
        searchTimeout () {
            var self = this;
            if (self.search_timer) {
                clearTimeout(self.search_timer);
                self.search_timer = null;
            }
            self.search_timer = setTimeout(() => {
                self.getFunds();
            }, 300);
        },

        // BUTTON :: Next page of datas.
        nextFundsPage () {
            var funds = this.funds;
            if (funds && funds.next_page_url) {
                this.current_page = this.getPageFromUrl(funds.next_page_url);
                this.getFunds();
            }
        },

        // BUTTON :: Previous page of datas.
        prevFundsPage () {
            var funds = this.funds;
            if (funds && funds.prev_page_url) {
                this.current_page = this.getPageFromUrl(funds.prev_page_url);
                this.getFunds();
            }
        },

        // HELPER :: Limit text.
        limitText (str, limit = 30) {
            if(str.length > limit) str = str.substring(0, limit) + '...';
            return str;
        },

        // HELPER :: Get page number from url.
        getPageFromUrl (url_string) {
            var url = new URL(url_string);
            var page = url.searchParams.get("page");
            return page;
        }


    }
});
</script>
@endsection