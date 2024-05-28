<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocena samopoczucia</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
        }

        .question {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        output {
            display: inline-block;
            margin-left: 10px;
            font-weight: bold;
        }

        #total_score {
            font-size: 20px;
            margin-top: 40px; /* większa przerwa między wynikami a przyciskiem */
        }

        /* Styl dla większego przycisku */
        .btn-primary {
            padding: 10px 20px; /* większe wymiary przycisku */
            font-size: 18px; /* większa czcionka */
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        // Array with questions
        $questions = array(
            "1. Jak oceniasz swoje ogólne samopoczucie w porównaniu z czasem przed leczeniem? (0% oznacza źle, 100% oznacza doskonale).",
            "2. Jak oceniasz swoją aktywność fizyczną w porównaniu z czasem przed leczeniem? (0% oznacza brak aktywności, 100% oznacza dużo aktywności).",
            "3. Jak oceniasz swoją jakość snu w porównaniu z czasem przed leczeniem? (0% oznacza bardzo złą jakość snu, 100% oznacza bardzo dobrą jakość snu).",
            "4. Jak oceniasz swoje codzienne funkcjonowanie w porównaniu z czasem przed leczeniem? (0% oznacza bardzo złe funkcjonowanie, 100% oznacza bardzo dobre funkcjonowanie).",
            "5. Jak oceniasz swoją jakość życia w porównaniu z czasem przed leczeniem? (0% oznacza bardzo złą jakość życia, 100% oznacza bardzo dobrą jakość życia)."
        );

        // Loop through each question and generate HTML
        foreach ($questions as $index => $question) {
        ?>
            <div class="question">
                <label><?php echo $question; ?></label>
            </div>

            <div class="form-group">
                <input type="range" name="slider_<?php echo $index; ?>" id="slider_<?php echo $index; ?>" min="0" max="100" step="1" value="0" class="form-control">
                <output for="slider_<?php echo $index; ?>" id="slider_<?php echo $index; ?>_value">0</output>
            </div>
        <?php
        }
        ?>

        <div id="total_score"></div>

        <!-- Wyślij -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="singlebutton"></label>
            <div class="col-md-4">
                <button id="singlebutton" name="singlebutton" class="btn btn-primary">Wyślij</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all sliders
            var sliders = document.querySelectorAll('input[type="range"]');
            var totalScoreElement = document.getElementById('total_score');

            // Function to calculate total score
            function calculateTotalScore() {
                var total = 0;
                sliders.forEach(function(slider) {
                    total += parseInt(slider.value);
                });
                return total;
            }

            // Function to display total score
            function displayTotalScore() {
                var totalScore = calculateTotalScore();
                totalScoreElement.textContent = "Suma wyników: " + totalScore;
            }

            // Attach event listener to each slider to update total score
            sliders.forEach(function(slider) {
                slider.addEventListener('input', function() {
                    var output = this.nextElementSibling;
                    output.textContent = this.value;
                    displayTotalScore();
                });
            });

            displayTotalScore(); // Display total score initially
        });
    </script>
</body>

</html>
