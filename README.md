<!DOCTYPE html>
<html>
<body>
    <h1>Contabo Storage Project Setup</h1>
    <ol>
        <!-- Clone the Repository -->
        <li>
            <strong>Clone the Repository:</strong>
            <ul>
                <li>Clone the Contabo Storage project repository from GitHub.</li>
                <li>Open your terminal or command prompt and navigate to the desired directory where you want to clone the repository.</li>
                <li>Run the following command: <code>git clone [repository_url]</code></li>
                <li>This will clone the repository to your local machine.</li>
            </ul>
        </li>
        <!-- Install Dependencies -->
        <li>
            <strong>Install Dependencies:</strong>
            <ul>
                <li>Navigate to the project directory: <code>cd contabo-storage</code></li>
                <li>Install the required dependencies using Composer. If you don't have Composer installed, make sure to install it first. Run: <code>composer install</code></li>
            </ul>
        </li>
        <!-- Set Up AWS Credentials -->
        <li>
            <strong>Set Up AWS Credentials:</strong>
            <ul>
                <li>Obtain your Contabo Storage access key and secret key from the Contabo Storage website.</li>
                <li>Open the <code>index.php</code> file in a text editor.</li>
                <li>Locate the following lines of code:
                    <pre>
$accessKey = 'your_access_key';
$secretKey = 'your_secret_key';
                    </pre>
                </li>
                <li>Replace <code>'your_access_key'</code> and <code>'your_secret_key'</code> with your actual Contabo Storage access key and secret key, respectively.</li>
                <li>Save the changes.</li>
            </ul>
        </li>
        <!-- Set Up Project Configuration -->
        <li>
            <strong>Set Up Project Configuration:</strong>
            <ul>
                <li>Open the <code>index.php</code> file in a text editor.</li>
                <li>Locate the following lines of code:
                    <pre>
$endpoint = 'YOUR_ENDPOINT'; // Set the endpoint for Contabo Storage
$region = 'YOUR_REGION'; // Set the region for Contabo Storage
$bucket = 'YOUR_BUCKET_NAME'; // Set the bucket name for Contabo Storage
                    </pre>
                </li>
                <li>Adjust the <code>$endpoint</code>, <code>$region</code>, and <code>$bucket</code> variables according to your Contabo Storage configuration.</li>
                <li>Save the changes.</li>
            </ul>
        </li>
        <!-- Start the Project -->
        <li>
            <strong>Start the Project:</strong>
            <ul>
                <li>Make sure you have a web server (e.g., Apache, Nginx) installed on your machine.</li>
                <li>Set up a virtual host or configure your web server to serve the Contabo Storage project from the project directory.</li>
                <li>Start your web server.</li>
                <li>Open your web browser and access the project using the configured URL.</li>
            </ul>
        </li>
        <!-- Project Usage -->
        <li>
            <strong>Project Usage:</strong>
            <ul>
                <li>Use the provided forms to upload files, create folders, and delete files.</li>
                <li>File uploads will be stored in the specified bucket on Contabo Storage.</li>
                <li>Folder creation and file deletion actions will be reflected in the Contabo Storage bucket.</li>
            </ul>
        </li>
    </ol>
</body>
</html>
