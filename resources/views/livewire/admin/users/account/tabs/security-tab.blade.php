<div>
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="heading mb-0">Security Summary</h4>
                </div>
                <div class="card-body pb-0">
                    <div class="row mb-2">
                        <div class="col-sm-4 col-6 mb-3">
                            <div class="bg-success-light rounded px-3 py-2 text-center">
                                <span class="fs-14 text-success">User Sign-in</span>
                                <h3 class="mb-0 fw-semibold">36,899</h3>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6 mb-3">
                            <div class="bg-primary-light rounded px-3 py-2 text-center">
                                <span class="fs-14 text-primary">Admin Sign-in</span>
                                <h3 class="mb-0 fw-semibold">72</h3>
                            </div>
                        </div>
                        <div class="col-sm-4 col-6 mb-3">
                            <div class="bg-danger-light rounded px-3 py-2 text-center">
                                <span class="fs-14 text-danger">Failed Attempts</span>
                                <h3 class="mb-0 fw-semibold">291</h3>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h4 class="heading mb-0">Activity Chart</h4>
                        <ul class="nav chart-summary-tab nav-pills" role="tablist">
                            <li class="nav-item ms-1">
                                <a class="nav-link btn btn-light text-secondary active" data-series="agents"
                                    data-bs-toggle="tab" href="#summaryAgents" role="tab">Agents</a>
                            </li>
                            <li class="nav-item ms-1">
                                <a class="nav-link btn btn-light text-secondary" data-series="clients"
                                    data-bs-toggle="tab" href="#summaryClients" role="tab">Clients</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clearfix overflow-hidden">
                    <div id="lineChartSecuritySummary"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="owl-carousel owl-theme card-carousel card-carousel-dots p-2 pt-3">
                                <div class="item">
                                    <span class="fs-14 mb-4 d-block">Recent Alerts</span>
                                    <h6 class="mb-2">Login Attempt Failed</h6>
                                    <p class="mb-4">To start a blog, think of a topic about good awesome first
                                        brainstorm details</p>
                                    <div class="d-flex justify-content-between pt-1">
                                        <span class="badge badge-sm border-0 badge-primary light">2 mins ago</span>
                                        <span class="badge badge-sm border-0 badge-light">Details</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="owl-carousel owl-theme card-carousel card-carousel-dots p-2 pt-3">
                                <div class="item">
                                    <span class="fs-14 mb-4 d-block">Security Guidelines</span>
                                    <h6 class="mb-2">Get Start Your Security</h6>
                                    <p class="mb-4 pb-1">To start a blog, think of a topic about good awesome first
                                        brainstorm details</p>
                                    <a href="javascript:void(0);" class="btn btn-primary light">Get Started</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6">
            <div class="card">
                <div class="card-body text-center p-3 pb-1">
                    <div class="clearfix">
                        <h4>Upgrade to Pro <br> Create Limitless Deals</h4>
                        <p>Craft a headline that is both informative and will capture creating an outline, and checking
                            facts</p>
                        <a href="javascript:void(0);" class="btn btn-primary">Upgrade Now</a>
                    </div>
                    <img class="view-light w-100" src="{{ asset('assets/users/images/upgrade-light.png') }}" alt="">
                    <img class="view-dark w-100" src="{{ asset('assets/users/images/upgrade-dark.png') }}" alt="">
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 py-3">
                    <h4 class="heading mb-0">License Usage</h4>
                    <div class="clearfix d-flex align-items-center">
                        <select class="default-select status-select normal-select">
                            <option value="All Time">All Time</option>
                            <option value="Today">Today</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Months">Months</option>
                        </select>
                        <div id="licenseUsageExcelBTN"></div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-1 table-striped-thead table-wide table-sm" id="tableLicenseUsage">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Operator</th>
                                    <th>IP Address</th>
                                    <th>Created</th>
                                    <th>API Keys</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-sm badge-success light border-0">License</span></td>
                                    <td>DSI: Workstation 2</td>
                                    <td>236.125.56.78</td>
                                    <td>2 mins ago</td>
                                    <td>
                                        <div class="select-text-wrap d-flex justify-content-between">
                                            <div class="text-select-copy">fftt456765gjkkjhi83093985</div>
                                            <button class="btn-select-text btn p-0 border-0 ms-4"><i
                                                    class="las la-copy fs-4"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>