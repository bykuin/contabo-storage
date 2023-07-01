<!DOCTYPE html>
<html>
<head>
    <title>Contabo Storage Test</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
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

        <!-- File Listing -->
        <div class="card mb-3">
            <div class="card-header">File Listing</div>
            <div class="card-body">
                <?php
              
                $files = $contaboStorage->listFiles();
                
                if (!empty($files)) {
                    echo '<ul>';
                    foreach ($files as $file) {
                        echo '<li>' . $file . ' <a href="?delete=' . urlencode($file) . '">Delete</a></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No files found.</p>';
                }
                ?>
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
                    echo '<div class="alert alert-danger" role="alert">File upload failed. Error: ' . $e->getMessage() . '</div>';
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
                    echo '<div class="alert alert-danger" role="alert">Folder creation failed. Error: ' . $e->getMessage() . '</div>';
                }
            }

            public function listFiles() {
                $files = [];

                try {
                    $objects = $this->s3Client->listObjects([
                        'Bucket' => $this->bucket,
                    ]);

                    foreach ($objects['Contents'] as $object) {
                        $files[] = $object['Key'];
                    }
                } catch (\Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">Failed to list files. Error: ' . $e->getMessage() . '</div>';
                }

                return $files;
            }

            public function deleteFile($file) {
                try {
                    $this->s3Client->deleteObject([
                        'Bucket' => $this->bucket,
                        'Key'    => $file,
                    ]);

                    echo '<div class="alert alert-success" role="alert">File deleted successfully: ' . $file . '</div>';
                } catch (\Exception $e) {
                    echo '<div class="alert alert-danger" role="alert">File deletion failed. Error: ' . $e->getMessage() . '</div>';
                }
            }

        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accessKey = 'YOUR_ACCESS_KEY';
            $secretKey = 'YOUR_SECRET_KEY'; 
            $endpoint = 'YOUR_ENDPOINT'; 
            $region = 'YOUR_REGION'; 
            $bucket = 'YOUR_BUCKET_NAME'; 
    

            $contaboStorage = new ContaboStorage($accessKey, $secretKey, $endpoint, $region, $bucket);

            if (isset($_FILES['file'])) {
                $file = $_FILES['file'];
                $contaboStorage->uploadFile($file);
            }

            if (isset($_POST['folderName'])) {
                $folderName = $_POST['folderName'];
                $contaboStorage->createFolder($folderName);
            }

            if (isset($_GET['delete'])) {
                $fileToDelete = $_GET['delete'];
                $contaboStorage->deleteFile($fileToDelete);
            }
        }
        ?>
    </div>
</body>
</html>
