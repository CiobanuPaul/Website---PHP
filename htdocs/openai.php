<?php
session_start();
if(isset($_SESSION['id_user'], $_SESSION['nume'])){
    $id_user = $_SESSION['id_user'];
    $nume = $_SESSION['nume'];
    $email = $_SESSION['email'];
    $tip_user = $_SESSION['tip_user'];
}
else{
    header("Location: https://paul-ciobanu.42web.io/signin.html");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
    <nav>
    <ul>
            <li><a href="activitati.php">Activități</a></li>
            <li><a href="date_personale.php">Date Personale</a></li>
            <?php
                if($tip_user == 'student')
                    echo '<li><a href="orar.php">Orar</a></li>';
                else if($tip_user == 'profesor')
                    echo '<li><a href="materii.php">Materii predate</a></li>';
                else
                    echo '<li><a href="info_admitere.php">Informatii admitere</a></li>';
            ?>
            <li><a href="openai.php">Asistenta chat 24/7</a></li>
            <li><a href="signout.php">Deconectare</a></li>
        </ul>
    </nav>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Input Form</title>
</head>
<body>


<p>Plasati intrebarea voastra asistentului FMI:</p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="question">Enter Text:</label>
    <input type="text" name="question" id="question" required>

    <button type="submit">Submit</button>
</form>

</body>
</html>



<?php
require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Now you can access your API key using $_ENV['OPENAI_API_KEY']
$openaiApiKey = $_ENV['OPENAI_API_KEY'];

$threadsEndpoint = "https://api.openai.com/v1/threads";

$headers = array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $openaiApiKey,
    "OpenAI-Beta: assistants=v1"
);

// Example for creating a thread
$threadData = array(
    // Add your specific thread data here, if needed
);

$chThreads = curl_init($threadsEndpoint);
curl_setopt($chThreads, CURLOPT_POST, 1);
curl_setopt($chThreads, CURLOPT_POSTFIELDS, json_encode($threadData));
curl_setopt($chThreads, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chThreads, CURLOPT_HTTPHEADER, $headers);

$responseThreads = curl_exec($chThreads);

if (curl_errno($chThreads)) {
    echo 'Error in threads request: ' . curl_error($chThreads);
} else {
    // echo 'Threads response: ' . $responseThreads;
}
curl_close($chThreads);
$thread = json_decode($responseThreads, true);
$thread_id = $thread['id'];
// echo $thread_id;



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST["question"];

    $threadMessagesEndpoint = "https://api.openai.com/v1/threads/$thread_id/messages";

    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $openaiApiKey,
        "OpenAI-Beta: assistants=v1"
    );

    $messageData = array(
        "role" => "user",
        "content" => $question
    );

    $chMessages = curl_init($threadMessagesEndpoint);
    curl_setopt($chMessages, CURLOPT_POST, 1);
    curl_setopt($chMessages, CURLOPT_POSTFIELDS, json_encode($messageData));
    curl_setopt($chMessages, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chMessages, CURLOPT_HTTPHEADER, $headers);

    $responseMessages = curl_exec($chMessages);

    if (curl_errno($chMessages)) {
        echo 'Error in messages request: ' . curl_error($chMessages);
    } else {
        // echo 'Messages response: ' . $responseMessages;
    }
    curl_close($chMessages);






    $threadRunsEndpoint = "https://api.openai.com/v1/threads/$thread_id/runs";
    $headers = array(
        "Authorization: Bearer " . $openaiApiKey,
        "Content-Type: application/json",
        "OpenAI-Beta: assistants=v1"
    );

    $runData = array(
        "assistant_id" => "asst_Lz9Z1Co3BdRTE8iGuzAVr0iQ"
    );

    $chRuns = curl_init($threadRunsEndpoint);
    curl_setopt($chRuns, CURLOPT_POST, 1);
    curl_setopt($chRuns, CURLOPT_POSTFIELDS, json_encode($runData));
    curl_setopt($chRuns, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chRuns, CURLOPT_HTTPHEADER, $headers);

    $responseRuns = curl_exec($chRuns);

    if (curl_errno($chRuns)) {
        echo 'Error in runs request: ' . curl_error($chRuns);
    } else {
        // echo 'Runs response: ' . $responseRuns;
    }
    curl_close($chRuns);



    $run = json_decode($responseRuns, true);
    $run_id = $run['id'];
    // echo $run_id;




    $threadRunStatusEndpoint = "https://api.openai.com/v1/threads/$thread_id/runs/$run_id";

    $headers = array(
        "Authorization: Bearer " . $openaiApiKey,
        "OpenAI-Beta: assistants=v1"
    );

    // Define the interval between each check (in seconds)
    $checkInterval = 1; // Adjust this as needed

    // Infinite loop to periodically check the run status
    while (true) {
        $chRunStatus = curl_init($threadRunStatusEndpoint);
        curl_setopt($chRunStatus, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chRunStatus, CURLOPT_HTTPHEADER, $headers);

        $responseRunStatus = curl_exec($chRunStatus);

        if (curl_errno($chRunStatus)) {
            echo 'Error in run status request: ' . curl_error($chRunStatus);
        } else {
            // You can parse $responseRunStatus as JSON to get the status details
            $statusData = json_decode($responseRunStatus, true);
            // Check the status and take appropriate action
            if ($statusData && isset($statusData['status'])) {
                $runStatus = $statusData['status'];
                // Perform further actions based on the run status
                if ($runStatus === 'completed') {
                    // The run has completed, you can retrieve the results or perform additional tasks
                    // For example, $statusData['data']['result'] contains the result of the run
                    break; // Exit the loop if the run is completed
                } elseif ($runStatus === 'failed') {
                    // The run has failed, handle accordingly
                    break; // Exit the loop if the run has failed
                } else {
                    // The run is still in progress, continue to check later
                }
            } else {
                echo 'Invalid or unexpected response format.' . "\n";
                break;
            }
        }

        curl_close($chRunStatus);

        // Sleep for the specified interval before the next check
        sleep($checkInterval);
    }


    // echo "OK<br>";


    $threadMessagesEndpoint = "https://api.openai.com/v1/threads/$thread_id/messages";

    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $openaiApiKey,
        "OpenAI-Beta: assistants=v1"
    );

    $chMessages = curl_init($threadMessagesEndpoint);
    curl_setopt($chMessages, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chMessages, CURLOPT_HTTPHEADER, $headers);

    $responseMessages = curl_exec($chMessages);

    if (curl_errno($chMessages)) {
        echo 'Error in messages request: ' . curl_error($chMessages);
    } else {
        // echo 'Messages response: ' . $responseMessages;
    }
    curl_close($chMessages);

    // echo "OK<br>";
    // echo "OK<br>";
    $responseMessages = json_decode($responseMessages, true);
    $answer = $responseMessages['data'][0]['content'][0]['text']['value'];

    echo "<p>".$answer."</p>";
}
?>


