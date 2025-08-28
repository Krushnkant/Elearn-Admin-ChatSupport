<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detailed Test Results</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; margin: 8px; line-height: 1.35; }
        h2 { margin: 4px 0; font-size: 14px; text-align: center; }
        p { margin: 2px 0; }

        .question { 
            margin-bottom: 16px;
            padding-bottom: 8px; 
            border-bottom: 1px dashed #ccc;
        }
        .q-title { font-weight: bold; margin-bottom: 4px; }

        .options { display: flex; flex-wrap: wrap; margin: 0 0 4px 12px; }
        .option { margin-right: 16px; white-space: nowrap; }
        .option-label { font-weight: bold; margin-right: 2px; }

        .answer { margin-left: 12px; font-size: 10.5px; margin-bottom: 2px; }
        .explanation { margin-left: 12px; font-style: italic; font-size: 9.5px; color: #444; }

        hr { margin: 10px 0; }
    </style>
</head>
<body>

    <h2>Detailed Test Results</h2>
    <p><strong>User:</strong> {{ $user->name }}</p>
    <p><strong>Total Attempted:</strong> {{ $total_attempt }}</p>
    <hr>

    @foreach($questionsList as $q)
        <div class="question">
            <div class="q-title">Q{{ $q['id'] }}. {{ $q['question'] }}</div>

            <div class="options">
                @foreach($q['options'] as $optionId => $optionText)
                    <div class="option">
                        <span class="option-label">{{ chr(64 + $loop->iteration) }}.</span> 
                        {{ $optionText }}
                    </div>
                @endforeach
            </div>

            <div class="answer">
                <strong>Your Answer:</strong> {{ $q['user_answer'] ?? 'Not Answered' }}
            </div>

            <div class="explanation">
                <strong>Explanation:</strong> {{ $q['explanation'] }}
            </div>
        </div>
        <br>
    @endforeach

</body>
</html>
