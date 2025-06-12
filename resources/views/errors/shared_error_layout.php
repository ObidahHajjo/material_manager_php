<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php if (isset($url) && $url) : ?>
        <meta http-equiv='refresh' content='8;url=<?= $url ?>'>
    <?php endif; ?>
    <title><?= $title ?? 'Error | Something went wrong' ?></title>
    <style>
        body {
            background: #f6f6f6;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
            padding: 40px;
        }

        .error-box {
            background: #fff;
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 6px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .error-title {
            font-size: 26px;
            margin-bottom: 10px;
            color: #c0392b;
        }

        .error-message {
            margin-bottom: 20px;
        }

        .trace {
            background: #f3f3f3;
            padding: 15px;
            font-size: 14px;
            border-radius: 4px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <div class="error-title"><?= $heading ?? 'An error occurred' ?></div>
        <div class="error-message">
            <?= htmlspecialchars($message) ?>
            <?php if ($url) : ?>
                <p style="color: red;">You will be redirect to <?= $url ?></p>
            <?php endif; ?>
        </div>

        <?php //if (true): 
        ?>
        <div><strong>File:</strong> <?= htmlspecialchars($file) ?> (line <?= $line ?>)</div>
        <br>
        <div class="trace">
            <pre><?= htmlspecialchars($trace) ?></pre>
        </div>
        <?php //else: 
        ?>
        <!-- <p>Please contact support if the issue persists.</p> -->
        <?php //endif; 
        ?>
    </div>
</body>

</html>