<!DOCTYPE html>
<html>
<head>
    <title>Contabo Storage Test </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            grid-gap: 20px;
            margin-top: 20px;
        }

        .file-card {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contabo Storage Test</h1>

        <!-- File Upload Form -->
        <div class="card mb-3">
            <div class="card-header">File Upload</div>
            <div class="card-body">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Select File:</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </form>
            </div>
        </div>

        <!-- Folder Creation Form -->
        <div class="card mb-3">
            <div class="card-header">Folder Creation</div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="folderName">Folder Name:</label>
                        <input type="text" class="form-control" id="folderName" name="folderName">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Folder</button>
                </form>
            </div>
        </div>

        <?php
        use Aws\S3\S3Client;

        require 'vendor/autoload.php';

        class ContaboStorage {
            private $s3Client;
            private $bucket;
            private $endpoint;

            function __construct($access_key, $secret_key, $endpoint, $region, $bucket) {
                $this->s3Client = new S3Client([
                    'credentials' => [
                        'key'    => $access_key,
                        'secret' => $secret_key,
                    ],
                    'region' => $region,
                    'version' => 'latest',
                    'endpoint' => $endpoint,
                    'use_path_style_endpoint' => true,
                    'options' => [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ],
                ]);
                $this->bucket = $bucket;
                $this->endpoint = $endpoint;
            }

            public function uploadFile($file) {
                try {
                    $key = basename($file['name']);
                    $body = fopen($file['tmp_name'], 'rb');

                    $this->s3Client->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $key,
                        'Body'   => $body,
                    ]);

                    echo '<div class="alert alert-success" role="alert">File uploaded successfully: ' . $key . '</div>';
                } catch (\Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Failed to upload file. Error: ' . $e->getMessage() . '</div>';
                }
            }

            public function createFolder($folderName) {
                try {
                    $key = rtrim($folderName, '/') . '/';

                    $this->s3Client->putObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $key,
                        'Body'   => '',
                    ]);

                    echo '<div class="alert alert-success" role="alert">Folder created successfully: ' . $folderName . '</div>';
                } catch (\Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Failed to create folder. Error: ' . $e->getMessage() . '</div>';
                }
            }

            public function listFiles() {
                try {
                    $result = $this->s3Client->listObjects([
                        'Bucket' => $this->bucket,
                    ]);

                    $files = [];
                    foreach ($result['Contents'] as $object) {
                        $files[] = $object['Key'];
                    }

                    return $files;
                } catch (\Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Failed to list files. Error: ' . $e->getMessage() . '</div>';
                }
            }

            public function deleteFile($file) {
                try {
                    $this->s3Client->deleteObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $file,
                    ]);

                    echo '<div class="alert alert-success" role="alert">File deleted successfully: ' . $file . '</div>';
                } catch (\Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Failed to delete file. Error: ' . $e->getMessage() . '</div>';
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accessKey = 'YOUR_ACCESS_KEY';
            $secretKey = 'YOUR_SECRET_KEY';
            $endpoint = 'YOUR_ENDPOINT';
            $region = 'YOUR_REGION';
            $bucket = 'YOUR_BUCKET';

            $contaboStorage = new ContaboStorage($accessKey, $secretKey, $endpoint, $region, $bucket);

            if (isset($_FILES['file'])) {
                $file = $_FILES['file'];
                $contaboStorage->uploadFile($file);
            }

            if (isset($_POST['folderName'])) {
                $folderName = $_POST['folderName'];
                $contaboStorage->createFolder($folderName);
            }
        }

        // List files
        if (isset($contaboStorage)) {
            $files = $contaboStorage->listFiles();

            echo '<h2>Files:</h2>';
            if (!empty($files)) {
                echo '<div class="file-grid">';
                foreach ($files as $file) {
                    echo '<div class="file-card">';
                    echo '<a href="?delete=' . $file . '" style="float: right; color: red;" onclick="return confirm(\'Are you sure you want to delete the file?\')">Delete</a>';
                    echo '<p>' . $file . '</p>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No files found.</p>';
            }
        }

        // Delete file if requested
        if (isset($contaboStorage) && isset($_GET['delete'])) {
            $fileToDelete = $_GET['delete'];
            $contaboStorage->deleteFile($fileToDelete);
        }
        ?>
    </div>
</body>
</html>
