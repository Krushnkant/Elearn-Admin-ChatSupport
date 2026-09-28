<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Assessment Result</title>
    <style>
        * { box-sizing: border-box; }

        body {
            position: relative;
            width: 19cm;
            margin: 0 auto;
            color: #1f2933;
            background: #ffffff;
            font-family: Arial, "Helvetica", sans-serif;
            font-size: 12px;
        }

        header { padding: 10px 0; margin-bottom: 18px; }

        #logo { text-align: center; margin-bottom: 8px; }
        #logo img { width: 80px; }

        h1.title {
            text-align: center;
            color: #34495e;
            font-size: 22px;
            font-weight: normal;
            letter-spacing: 1px;
            margin: 0;
            padding: 8px 0;
            border-top: 1px solid #cbd5e0;
            border-bottom: 1px solid #cbd5e0;
        }

        h2.section {
            color: #34495e;
            font-size: 15px;
            font-weight: bold;
            margin: 22px 0 10px 0;
            padding-bottom: 4px;
            border-bottom: 2px solid #4e79a7;
        }

        /* ---- Summary card ---- */
        table.summary { width: 100%; border-collapse: collapse; }
        table.summary td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }
        table.summary td.label {
            width: 34%;
            color: #52606d;
            background: #f7fafc;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 3px;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
        }
        .badge.pass { background: #2e8b57; }
        .badge.fail { background: #d64545; }

        /* ---- Question overview counts ---- */
        table.counts { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.counts td {
            width: 25%;
            text-align: center;
            border: 1px solid #e2e8f0;
            padding: 10px 6px;
        }
        table.counts .num { font-size: 20px; font-weight: bold; }
        table.counts .cap { font-size: 11px; color: #52606d; text-transform: uppercase; }
        .c-correct { color: #2e8b57; }
        .c-wrong   { color: #d64545; }
        .c-skip    { color: #8a94a6; }
        .c-total   { color: #34495e; }

        /* ---- Question dots ---- */
        .legend { margin-bottom: 10px; font-size: 11px; color: #52606d; }
        .legend .dot { margin: 0 4px 0 12px; }
        .dot {
            display: inline-block;
            width: 13px;
            height: 13px;
            border-radius: 7px;
            vertical-align: middle;
            line-height: 13px;
        }
        .dot.correct { background: #2e8b57; }
        .dot.wrong   { background: #d64545; }
        .dot.skip    { background: #c2c9d1; }

        .dotgrid {
            border: 1px solid #e2e8f0;
            padding: 10px 8px 4px 12px;
        }
        .q {
            display: inline-block;
            width: 30px;
            text-align: center;
            margin-bottom: 8px;
        }
        .q .dot { display: block; margin: 0 auto 3px auto; }
        .q .n { font-size: 9px; color: #8a94a6; }

        /* ---- Domain bar chart ---- */
        table.bars { width: 100%; border-collapse: collapse; }
        table.bars td { padding: 5px 6px; vertical-align: middle; border: none; }
        td.bar-label { width: 30%; font-weight: bold; color: #34495e; font-size: 12px; }
        td.bar-track { width: 55%; }
        td.bar-val { width: 15%; text-align: right; font-weight: bold; color: #34495e; }
        table.track { width: 100%; border-collapse: collapse; }
        table.track td { padding: 0; height: 16px; border: none; }
        table.track td.fill { background: #4e79a7; }
        table.track td.rest { background: #edf1f5; }

        /* ---- Detailed breakdown table ---- */
        table.detail { width: 100%; border-collapse: collapse; }
        table.detail th {
            background: #34495e;
            color: #ffffff;
            font-weight: bold;
            padding: 8px 10px;
            font-size: 11px;
            text-align: center;
        }
        table.detail th.left, table.detail td.left { text-align: left; }
        table.detail td {
            padding: 7px 10px;
            border-bottom: 1px solid #e8ecf1;
            text-align: center;
            font-size: 11px;
        }
        table.detail tr.group td {
            background: #eef2f7;
            font-weight: bold;
            color: #2c5282;
        }

        footer {
            margin-top: 26px;
            color: #8a94a6;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        tr { page-break-inside: avoid; }
    </style>
</head>

<body>
    @php
        // Works whether $data['data'] rows are arrays or Eloquent models (both ArrayAccess).
        $rows = $data['data'];
        $correctCount = 0; $wrongCount = 0; $skipCount = 0;
        foreach ($rows as $r) {
            $ic = isset($r['is_correct']) ? $r['is_correct'] : '';
            if ($ic === 'correct')        { $correctCount++; }
            elseif ($ic === 'incorrect')  { $wrongCount++; }
            else                          { $skipCount++; }
        }
        $totalQ = count($rows);
        $statusText = trim(strip_tags((string) ($data['status'] ?? '')));
        $isPass = strtolower($statusText) === 'pass';
    @endphp

    <header>
        <div id="logo">
            @php
                // Embed the local logo so dompdf never fetches it over the network
                // (the remote fetch added seconds to every PDF render).
                $logoFile = public_path('Admin/kwmain.png');
                $logoSrc  = is_file($logoFile)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoFile))
                    : 'https://chatsupport.co.in/public/Admin/kwmain.png';
            @endphp
            <img src="{{ $logoSrc }}" alt="Logo">
        </div>
        <h1 class="title">Assessment Result</h1>
    </header>

    <table class="summary">
        <tr>
            <td class="label">Student Name</td>
            <td>{{ $user->name ?? '--' }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td><span class="badge {{ $isPass ? 'pass' : 'fail' }}">{{ $statusText ?: '--' }}</span></td>
        </tr>
        <tr>
            <td class="label">Percentage Scored</td>
            <td>{{ $data['total_scored'] ?? '--' }}</td>
        </tr>
        <tr>
            <td class="label">Passing Percentage</td>
            <td>{{ $data['passing_percentage'] ?? '--' }}</td>
        </tr>
        <tr>
            <td class="label">Assessment Completion Date</td>
            <td>{{ $data['assessment']['assessment_campletion_date'] ?? '--' }}</td>
        </tr>
    </table>

    <h2 class="section">Question Overview</h2>
    <table class="counts">
        <tr>
            <td><div class="num c-correct">{{ $correctCount }}</div><div class="cap">Correct</div></td>
            <td><div class="num c-wrong">{{ $wrongCount }}</div><div class="cap">Wrong</div></td>
            <td><div class="num c-skip">{{ $skipCount }}</div><div class="cap">Unattempted</div></td>
            <td><div class="num c-total">{{ $totalQ }}</div><div class="cap">Total</div></td>
        </tr>
    </table>

    <div class="legend">
        <span class="dot correct"></span>Correct
        <span class="dot wrong"></span>Wrong
        <span class="dot skip"></span>Unattempted
    </div>
    <div class="dotgrid">
        @foreach ($rows as $key => $result)
            @php
                $ic = isset($result['is_correct']) ? $result['is_correct'] : '';
                $cls = $ic === 'correct' ? 'correct' : ($ic === 'incorrect' ? 'wrong' : 'skip');
            @endphp
            <span class="q"><span class="dot {{ $cls }}"></span><span class="n">{{ $key + 1 }}</span></span>
        @endforeach
    </div>

    <h2 class="section">Performance by Domain</h2>
    <table class="bars">
        @foreach ($data['categoryWiseReport'] as $group)
            @php $w = max(0, min(100, (int) round($group['scored']))); @endphp
            <tr>
                <td class="bar-label">{{ $group['title'] }}</td>
                <td class="bar-track">
                    <table class="track">
                        <tr>
                            @if ($w > 0)<td class="fill" style="width: {{ $w }}%;"></td>@endif
                            @if ($w < 100)<td class="rest" style="width: {{ 100 - $w }}%;"></td>@endif
                        </tr>
                    </table>
                </td>
                <td class="bar-val">{{ $w }}%</td>
            </tr>
        @endforeach
    </table>

    <h2 class="section">Detailed Breakdown</h2>
    <table class="detail">
        <thead>
            <tr>
                <th class="left">Category</th>
                <th>Total Questions</th>
                <th>Correct Questions</th>
                <th>Percentage Scored</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['categoryWiseReport'] as $group)
                <tr class="group">
                    <td class="left">{{ $group['title'] }}</td>
                    <td>{{ $group['totalQuestion'] }}</td>
                    <td>{{ $group['correctQuestion'] }}</td>
                    <td>{{ round($group['scored']) }}%</td>
                </tr>
                @foreach ($group['category'] as $category)
                    <tr>
                        <td class="left">{{ $category['name'] }}</td>
                        <td>{{ $category['totalQuestion'] }}</td>
                        <td>{{ $category['correctQuestion'] }}</td>
                        <td>{{ round($category['scored']) }}%</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <footer>
        Copyright &copy; {{ date('Y') }} Knowledgewoods. All rights reserved.
    </footer>
</body>

</html>
