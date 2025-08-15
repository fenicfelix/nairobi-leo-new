@extends('backend.layouts.backend')

@section( 'main_body')

    <!-- Main body -->
      <div id="main-body">

        <div class="row g-4">
          <div class="col-12 col-md-4">
            <div class="row g-4">
              <div class="col-md-3 col-lg-6 col-6">
                <div class="card h-100">
                  <div class="card-body">
                    <p class="mb-0 text-secondary">Today's Posts</p>
                    <h3 class="fw-black mt-3 m-4">{{ number_format($todays_posts) }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-md-3 col-lg-6 col-6">
                <div class="card h-100">
                  <div class="card-body">
                    <p class="mb-0 text-secondary">Total Posts</p>
                    <h3 class="fw-black mt-3 mb-4">{{ number_format($total_posts) }}</h3>
                  </div>
                </div>
              </div>
              <div class="col-lg-12 col-md-6 col-12">
                <div class="card h-100">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-6">
                        <h3 class="fw-black mb-2">Daily Performance</h3>
                        <p class="small text-secondary mt-3 mb-1">Today's PageViews</p>
                        <h4 class="my-3">{{ ($total_views) ? number_format($total_views->total) : '0' }}</h4>
                        <p class="card-text text-secondary small">
                          <span class="font-weight-bolder mt-4">Gathered Today</span>
                        </p>
                      </div>
                      <div class="col-6">
                        <div id="earnings"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-8">
            <div class="card h-100">
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="d-sm-flex justify-content-between align-items-center">
                      <div>
                        <h6 class="mb-sm-0"><b>Daily Pageviews Report</b></h6>
                        <small class="text-secondary">Report for the month of {{ date('M. Y')}}</small>
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="btn-group mb-3">
                          {{-- <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Period
                          </button>
                          <ul class="dropdown-menu">
                            <li><button class="dropdown-item" type="button">This Month</button></li>
                            <li><button class="dropdown-item" type="button">Last Month</button></li>
                            <li><button class="dropdown-item" type="button">All TIme</button></li>
                          </ul> --}}
                        </div>
                      </div>
                    </div>
                    <div id="revenue"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row mt-2 g-4">
          <div class="col-12 col-md-3">
            <div class="card h-100">
              <div class="card-header border-bottom-0 d-flex justify-content-between">
                <div>
                  <h6 class="mb-0"><b>Top Publishers</b></h6>
                  <small class="text-secondary">All time counter</small>
                </div>
              </div>
              <div class="card-body">
                @forelse ($top_authors as $author)
                  <div class="d-flex align-items-center mb-3">
                    @php
                        $thumbnail = "https://ui-avatars.com/api/?name=" . $author->first_name . "+" . $author->last_name . "&color=4c4e52&background=bdbec1";
                        if ($author->thumbnail) {
                            $thumbnail = Storage::disk('public')->url($author->thumbnail);
                        }
                    @endphp
                    <img class="rounded-circle" src="{{ $thumbnail }}" alt="" width="30" height="30" loading="lazy">
                    <span class="ms-2">{{ $author->first_name." ".$author->last_name}}</span>
                    <span class="ms-auto me-2">{{ ($author->total_views) ? number_format((($author->views/$author->total_views)*100), 2) : "0" }}%</span>
                  </div>
                @empty
                    
                @endforelse
              </div>
            </div>
          </div>
          <div class="col-12 col-md-9">
            <div class="card h-100">
              <div class="card-header border-bottom-0 d-flex justify-content-between">
                <div>
                  <h6 class="mb-0"><b>Top 10 Stories</b></h6>
                  <small class="text-secondary">All time counter</small>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Title</th>
                        <th scope="col">Category</th>
                        <th scope="col">Views</th>
                        <th scope="col">%</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $i=1;
                      @endphp
                      @forelse ($top_stories as $post)
                        @php
                            $permalink = route('post', [$post->category_slug, $post->id, $post->slug]);
                        @endphp
                          <tr>
                            <td>{{ $i++ }}</td>
                            <td><a href="{{ $permalink }}" target="blank">{{ $post->title }}</a></td>
                            <td>{{ $post->category_name }}</td>
                            <td>{{ number_format($post->views) }}</td>
                            <td>{{ ($post->total_views > 0) ? number_format((($post->views/$post->total_views)*100), 2) : 0 }}%</td>
                          </tr>
                      @empty
                          
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- /Main body -->

@endsection

@section('scripts')

      <script>
        let graph = {!! $graph !!};
        new ApexCharts(document.querySelector('#earnings'), {
          series: [2036, 245378],
          chart: {
            height: 150,
            type: 'donut',
            toolbar: {
              show: false,
            },
          },
          labels: ['Today', 'Other Days'],
          dataLabels: {
            enabled: false,
          },
          legend: {
            show: false,
          },
          stroke: {
            width: 0,
          },
          colors: ['#0dcaf0', '#facc15'],
        }).render()

        new ApexCharts(document.querySelector('#revenue'), {
      chart: {
        type: 'line',
        height: 260,
        toolbar: {
          show: false,
        }
      },
      yaxis: [{labels: {formatter: function(val) {return val.toFixed(0)}}}],
      colors: ['#6366f1'],
      series: [
        {
          name: 'Pageviews',
          data: graph.data
        },
      ],
      xaxis: {
        categories: graph.days
      },
      plotOptions: {
        bar: {
          columnWidth: '30%',
          borderRadius: 4,
        },
      },
      dataLabels: {
        enabled: false
      },
      legend: {
        show: false,
      }
    }).render()
      </script>

@endsection