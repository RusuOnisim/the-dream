<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
            href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital@0;1&display=swap"
            rel="stylesheet"
    />
    <meta name="description" content="Effortlessly convert currencies with our user-friendly online currency converter. Instantly calculate exchange rates and perform accurate currency conversions for a seamless financial experience."/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="style.css"/>

    <title>The Dream</title>
</head>
<body>
<div class="top">
    <h2 class="title">Currency Converter</h2>
    <hr/>
</div>

<div class="container">
    <div class="container1">
    <form method="post" action="">
    <label for="fromCurrency">From:</label>
    <select id="fromCurrency" name="fromCurrency" required>
        <option value="EUR" <?php if(isset($_POST['fromCurrency']) && $_POST['fromCurrency'] == 'EUR') echo 'selected'; ?>>EUR 🇪🇺 (Euro)</option>
        <option value="RON" <?php if(isset($_POST['fromCurrency']) && $_POST['fromCurrency'] == 'RON') echo 'selected'; ?>>RON 🇷🇴 (Romanian leu)</option>
        <option value="USD" <?php if(isset($_POST['fromCurrency']) && $_POST['fromCurrency'] == 'USD') echo 'selected'; ?>>USD 🇺🇸 (US Dollar)</option>
        <option value="CAD" <?php if(isset($_POST['fromCurrency']) && $_POST['fromCurrency'] == 'CAD') echo 'selected'; ?>>CAD 🇨🇦 (Canadian Dollar)</option>
        <option value="AUD" <?php if(isset($_POST['fromCurrency']) && $_POST['fromCurrency'] == 'AUD') echo 'selected'; ?>>AUD 🇦🇺 (Australian Dollar)</option>
    </select>

    <label for="toCurrency">To:</label>
    <select id="toCurrency" name="toCurrency" required>
        <option value="USD" <?php if(isset($_POST['toCurrency']) && $_POST['toCurrency'] == 'USD') echo 'selected'; ?>>USD 🇺🇸 (US Dollar)</option>
        <option value="RON" <?php if(isset($_POST['toCurrency']) && $_POST['toCurrency'] == 'RON') echo 'selected'; ?>>RON 🇷🇴 (Romanian leu)</option>
        <option value="EUR" <?php if(isset($_POST['toCurrency']) && $_POST['toCurrency'] == 'EUR') echo 'selected'; ?>>EUR 🇪🇺 (Euro)</option>
        <option value="CAD" <?php if(isset($_POST['toCurrency']) && $_POST['toCurrency'] == 'CAD') echo 'selected'; ?>>CAD 🇨🇦 (Canadian Dollar)</option>
        <option value="AUD" <?php if(isset($_POST['toCurrency']) && $_POST['toCurrency'] == 'AUD') echo 'selected'; ?>>AUD 🇦🇺 (Australian Dollar)</option>
    </select>

    <label for="amount">Amount:</label>
    <input type="number" id="amount" name="amount" value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>" required inputmode="numeric">

    <button type="submit" name="convert">Convert</button>
</form>
        <?php

session_start();

if (isset($_POST['convert'])) {
    $fromCurrency = strtoupper($_POST['fromCurrency']);
    $toCurrency = strtoupper($_POST['toCurrency']);
    $amount = $_POST['amount'];

    $url = "https://currency-converter5.p.rapidapi.com/currency/convert?format=json&from=$fromCurrency&to=$toCurrency&amount=$amount";

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: currency-converter5.p.rapidapi.com",
            "X-RapidAPI-Key: f184adeb6dmsh699dc86882844f5p12c3b5jsn3de6bad7431d"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $result = json_decode($response, true);

        if (isset($result['rates'][$toCurrency]['rate'])) {
            $exchangeRate = $result['rates'][$toCurrency]['rate'];
            $convertedAmount = $amount * $exchangeRate;

            $conversion = [
                'fromCurrency' => $fromCurrency,
                'toCurrency' => $toCurrency,
                'amount' => $amount,
                'exchangeRate' => $exchangeRate,
                'convertedAmount' => $convertedAmount
            ];

            $_SESSION['conversionHistory'][] = $conversion;

            echo '<div class="result">';
            echo "<p class='text1'>Exchange Rate: $exchangeRate $toCurrency</p>";
            echo "<p class='text2'>Converted Amount: $convertedAmount $toCurrency</p>";
            echo '</div>';
        } else {
            echo "<p>Error: Unable to retrieve exchange rate from the API response.</p>";
        }
    }
}
?>

    </div>
    <div class="container2">
        <?php
        if (!empty($_SESSION['conversionHistory'])) {
            echo '<div class="history">';
            echo '<h3>Conversion History</h3>';
            echo '<ul>';
            foreach (array_slice(array_reverse($_SESSION['conversionHistory']), 0, 5) as $conversion) {
                echo '<li>';
                echo "Converted {$conversion['amount']} {$conversion['fromCurrency']} to {$conversion['convertedAmount']} {$conversion['toCurrency']}";
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>
</div>
</body>
</html>
