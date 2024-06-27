<!DOCTYPE html>
<html>
<head>
    <title>Work Hours Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h3 {
            margin-bottom: 5px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="datetime-local"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ced4da;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f1f1f1;
        }
        p {
            margin-top: 20px;
            text-align: center;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <h1>Work Hours Calculator</h1>
    <form method="post" action="">
        <?php
        $daysOfWeek = ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma'];

        foreach ($daysOfWeek as $day) {
            echo "<h3>$day</h3>";
            echo '<label for="arrived_'.$day.'">Kirish vaqti:</label>';
            echo '<input type="datetime-local" id="arrived_'.$day.'" name="arrived_'.$day.'" required><br>';

            echo '<label for="leaved_'.$day.'">Chiqish vaqti:</label>';
            echo '<input type="datetime-local" id="leaved_'.$day.'" name="leaved_'.$day.'" required><br><br>';
        }
        ?>
        <input type="submit" value="Submit">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        class WorkDay {
            public $arrived;
            public $leaved;
            public $creditTime;
            public $totalWorkedHours;

            public function __construct($arrived, $leaved) {
                $this->arrived = $arrived;
                $this->leaved = $leaved;
                $this->calculateWorkDuration();
            }

            private function calculateWorkDuration() {
                $time_to_work = strtotime($this->arrived);
                $left = strtotime($this->leaved);
                $duration = ($left - $time_to_work) / 3600; 
                $this->creditTime = $duration - 8;
                $this->totalWorkedHours = $duration;
            }
        }

        class WorkWeek {
            public $workDays = [];
            public $totalCreditTime = 0;
            public $totalWorkedHours = 0;

            public function addWorkDay($workDay) {
                $this->workDays[] = $workDay;
                $this->totalCreditTime += $workDay->creditTime;
                $this->totalWorkedHours += $workDay->totalWorkedHours;
            }

            public function sortWorkDays() {
                usort($this->workDays, function($a, $b) {
                    return strcmp($a->arrived, $b->arrived);
                });
            }

            public function displayResults() {
                echo "<h2>Results:</h2>";
                echo "<table>";
                echo "<tr><th>Kirish vaqti</th><th>Chiqish vaqti</th><th>Kredit vaqti (soat)</th><th>Jami ishlangan soatlar</th></tr>";

                foreach ($this->workDays as $day) {
                    echo "<tr>";
                    echo "<td>".$day->arrived."</td>";
                    echo "<td>".$day->leaved."</td>";
                    echo "<td>".$day->creditTime."</td>";
                    echo "<td>".$day->totalWorkedHours."</td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "<p>Umumiy ish soatlari qarzi: " . $this->totalCreditTime . " soat</p>";
                echo "<p>Jami ishlangan soatlar: " . $this->totalWorkedHours . " soat</p>";
            }
        }

        $workWeek = new WorkWeek();

        foreach ($daysOfWeek as $day) {
            $arrived = $_POST['arrived_'.$day];
            $leaved = $_POST['leaved_'.$day];
            $workDay = new WorkDay($arrived, $leaved);
            $workWeek->addWorkDay($workDay);
        }

        $workWeek->sortWorkDays();
        $workWeek->displayResults();
    }
    ?>
</body>
</html>
