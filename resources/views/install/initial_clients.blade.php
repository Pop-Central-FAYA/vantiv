    <div class="row" style="padding: 5%">

        <div class="col-xs-12">

            <div class="col-md-11">

                <div class="row">

                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Area Chart</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="barChart" style="height:250px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Line Chart</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="pieChart" style="height:250px"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="box box-success" style="display: none">
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="areaChart" style="height:230px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        <div class="client-box">
            <div class="col-md-2">
                <div class="cb-box">
                    <a href="{{ route('clients.create') }}">
                        <span data-toggle="modal" data-target=".bs-example1-modal-lg" style="cursor: pointer;">
                            <p align="center">
                                <img src="{{ asset('public/img/add-icon.png') }}" width="70%"> <br />
                                <h4 align="center">Add Client</h4>
                            </p>
                        </span>
                    </a>
                </div>
            </div>
            @foreach ($clients as $client)
                <div class="col-md-2">
                    <a href="{{ route('client.show', ['client_id' => $client['client_id']]) }}">
                        <div class="cb-box">
                            <p align="center">
                                <img src="{{ asset($client['image_url']) }}" width="70%"> <br />
                                <h4 align="center">{{ $client['name'] }}</h4>
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

    </div>

