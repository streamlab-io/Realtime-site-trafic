<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
    </head>
    <body>
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>
            @endif
                <div id="chartContainer" style="height: 400px; width: 100%;">
                </div>
            <div id="userChart" style="height: 400px; width: 100%;">
            </div>
         <script src="/js/canvasjs.min.js"></script>
         <script src="/StreamLab/StreamLab.js"></script>
            <script type="text/javascript">
                var user = 1;
                var sls = new StreamLabSocket({
                    appId:"{{ config('stream_lab.app_id') }}",
                    channelName:"test",
                    event:"*",
                    @if(Auth::check())
                        user_id:{{ auth()->user()->id }},
                        user_secret:"{{ md5(auth()->user()->name.auth()->user()->id) }}"
                    @endif
                });
                var slh = new StreamLabHtml();
                window.onload = function () {
                    var chart = new CanvasJS.Chart("chartContainer", {
                        title: {
                            text: "site Traffic",
                            fontSize: 30
                        },
                        animationEnabled: true,
                        axisX: {
                            gridColor: "Silver",
                        },
                        toolTip: {
                            shared: true
                        },
                        theme: "theme2",
                        axisY: {
                            gridColor: "Silver",
                        },
                        legend: {
                            verticalAlign: "center",
                            horizontalAlign: "right"
                        },
                        data: [
                            {
                                type: "line",
                                showInLegend: true,
                                lineThickness: 2,
                                name: "Visits",
                                markerType: "square",
                                color: "#F08080",
                                dataPoints: []
                            }
                        ]
                    });
                    var chartUser = new CanvasJS.Chart("userChart", {
                        title: {
                            text: "site Traffic",
                            fontSize: 30
                        },
                        animationEnabled: true,
                        axisX: {
                            gridColor: "Silver",
                        },
                        toolTip: {
                            shared: true
                        },
                        theme: "theme2",
                        axisY: {
                            gridColor: "Silver",
                        },
                        legend: {
                            verticalAlign: "center",
                            horizontalAlign: "right"
                        },
                        data: [
                            {
                                type: "line",
                                showInLegend: true,
                                lineThickness: 2,
                                name: "Visits",
                                markerType: "square",
                                color: "#F08080",
                                dataPoints: [{y:1}]
                            }
                        ]
                    });
                    sls.socket.onmessage = function(result){
                        slh.setData(result);
                        if(slh.getSource() == 'channels'){
                            chart.data[0].addTo("dataPoints" , {y:slh.getOnline()});
                        }

                        slh.updateUserList(function(id){
                            user = user+1;
                            chartUser.data[0].addTo("dataPoints" , {y:user});
                        } , function(id){
                            if(user > 0){
                                user = user-1;
                            }
                            chartUser.data[0].addTo("dataPoints" , {y:user});
                        });
                    };
                    chart.render();
                    chartUser.render();
                }
            </script>
    </body>
</html>
