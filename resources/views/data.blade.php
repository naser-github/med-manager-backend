<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>PATID</th>
                    <th>AGEY</th>
                    <th>AGEM</th>
                    <th>AGED</th>
                    <th>SEX</th>
                    @foreach($testcode as $tc)
                        <th>TESTCODE {{ $tc->TESTCODE  }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($query as $key => $q)
                    <tr>
                        <td></td>
                        <td>{{ $q->PATID}}</td>
                        <td>{{ $q->AGEY}}</td>
                        <td>{{ $q->AGEM}}</td>
                        <td>{{ $q->AGED}}</td>
                        <td>{{ $q->SEX}}</td>
                        @foreach($testcode as $key_t => $tc)
                            <td class="hidden">{{$test = 'TESTCODE'.$tc->TESTCODE}}</td>
                            <td>
                                {{$q->$test}}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
