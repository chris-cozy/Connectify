<?php
	session_start();
	include_once 'connection.php'
?>
<!DOCTYPE html>
<style>
    .text{
		color: white;
		font-family: monospace;
		align-items: center;
		text-decoration: none;
	}
	.logo{
		color: white;
		font-family: monospace;
		font-size: 25px;
		cursor: pointer;
	}
	.navbar{
		width: 100%;
		height: 15vh;
		margin: auto;
		display: flex;
		align-items: center;
	}
	.headnav{
		flex: 1;
		padding-left: 100px;
	}
	nav ul li{
		display: inline-block;
		list-style: none;
		margin: 0px 60px;
	}
	nav ul li a{
		text-decoration: none;
		color: rgb(255, 255, 255);
		text-align: center;
	}
	.profile{
		display: inline-block;
		color: white;
		margin: auto;
		align-items: center;
	}
</style>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Metube: Profile</title>
    </head>
    <body style="background-color: rgb(42, 44, 44);">
        <header>
            <h2 class="text"><a href="../MeTube.php" class="text">MeTube<3</a></h2>
            <h3 class="text"><?php echo $_SESSION['userID']; ?></h3>
			<?php
				echo $_SESSION['userID'];
			?>
        </header>
        <main>
            <section>
				<div class="navbar">
					<nav>
						<ul class="text">
							<li><a href="user_profile.php" ><b>Media</b></a></li>
							<li><a href="./playlists.php" >Playlists</a></li>
							<li><a href="./friends.php" >Friends</a></li>
                            <li><a href="./about.php" >About</a></li>
                            <li><a href="user_profile.php" >Messages</a></li>
							<li><a href="updateprofile.php">Update Profile</a></li>
							<li><a href="upload.php">Upload</a></li>
							<li><a href="messages.php">Messages</a></li>
						</ul>
					</nav>
				</div>
			</section>
            <hr>
			<!--Display recent uploaded medias-->
            <section>
				<h2 class="text">Your Videos</h2>
				<div>
					<?php
					$i = 0;
					$uid = $_SESSION['userID'];
					//WATCH PREPARED STATEMENTS VIDEO
					$extensions_arr = array("mp4","avi","3gp","mov","mpeg");
					$fetchVideos = mysqli_query($conn, "SELECT * FROM media WHERE userID ='$uid' AND type = 'video' ORDER BY mediaID DESC;") or die ("Query error".mysqli_error($conn)."\n");

					$resultCheck = mysqli_num_rows($fetchVideos);
					if ($resultCheck > 0){
						do{
							$row = mysqli_fetch_assoc($fetchVideos);
							if (isset($row['loc'])){
								$location = $row['loc'];
								$name = $row['title'];
								echo "<span style= 'display: inline-block;'>
										<video src='".$location."' controls width='200px'>This video could not be displayed :/</video>
										<br>
										<span><a href='../media_content.php?mediaID='".$row['mediaID']."''>".$name."</a></span>
									</span>";
							}
							$i++;

						}while($row && $i < 4 && $i < $resultCheck);
					}
					?>
				</div>
				<br>
				<hr>
				<br>
			</section>
			<section>
				<h2 class="text">Your Audio</h2>
				<div>
					<?php
					$uid = $_SESSION['userID'];
					$i = 0;
					$extensions_arr = array("mp3");
					$fetchVideos = mysqli_query($conn, "SELECT * FROM media WHERE userID ='$uid' AND type = 'audio' ORDER BY mediaID DESC;") or die ("Query error".mysqli_error($conn)."\n");

					$resultCheck = mysqli_num_rows($fetchVideos);
					if ($resultCheck > 0){
						do{
							$row = mysqli_fetch_assoc($fetchVideos);
							if (isset($row['loc'])){
								$location = $row['loc'];
								$name = $row['title'];
								echo "<span style= 'display: inline-block;'>
										<audio src='".$location."' controls type='audio/mpeg'>This audio could not be displayed :/</audio>
										<br>
										<span><a href='../media_content.php?mediaID='".$row['mediaID']."''>".$name."</a></span>
									</span>";
							}
							$i++;
						}while($row && $i < 4 && $i < $resultCheck);
					}
					?>
				</div>
				<br>
				<hr>
				<br>
			</section>
			<section>
				<h2 class="text">Your Images</h2>
				<div>
					<?php
					$uid = $_SESSION['userID'];
					$i = 0;
					$extensions_arr = array("jpg","png");
					$fetchVideos = mysqli_query($conn, "SELECT * FROM media WHERE userID ='$uid' AND type = 'image' ORDER BY mediaID DESC;") or die ("Query error".mysqli_error($conn)."\n");

					$resultCheck = mysqli_num_rows($fetchVideos);

					if ($resultCheck > 0){
						do{
							$row = mysqli_fetch_assoc($fetchVideos);
							if (isset($row['loc'])){
								$location = $row['loc'];
								$name = $row['title'];
								echo "<span style= 'display: inline-block;'>
										<img src='".$location."' width='200' alt='This image could not be displayed :/'/>
										<br>
										<span><a href='../media_content.php?mediaID='".$row['mediaID']."''>".$name."</a></span>
									</span>";
							}
							$i++;

						}while($row && $i < 4 && $i < $resultCheck);
					}
					?>
				</div>
			</section>
			<br>
			<hr>
			<section>
             <h2 class='text'>Favorites</h2>
             <?php
              $uid = $_SESSION['userID'];
              $query = mysqli_query($conn, "SELECT * FROM media_favorited WHERE userID = '$uid';") or die ("Query error".mysqli_error($conn)."\n");

              $resultCheck = mysqli_num_rows($query);
              if ($resultCheck > 0){
                do{
                  $row = mysqli_fetch_assoc($query);
                  if (isset($row['mediaID'])){
                    $mediaID = $row['mediaID'];

                    $query = mysqli_query($conn, "SELECT * FROM media WHERE mediaID='$mediaID';") or die ("Query error".mysqli_error($conn)."\n");
                    $resultCheck = mysqli_num_rows($query);

                    if ($resultCheck > 0){
                      $row = mysqli_fetch_assoc($query);
                      if (isset($row['loc'])){
                        $location = $row['loc'];
                        $name = $row['title'];
                        $type = $row['type'];
                        $description = $row['description'];
                        $creatorID = $row['userID'];
                        $creator = "";

                        $query = mysqli_query($conn, "SELECT * FROM user_info WHERE userID='$creatorID';") or die ("Query error".mysqli_error($conn)."\n");
                        $resultCheck = mysqli_num_rows($query);

                        if ($resultCheck > 0){
                          $row = mysqli_fetch_assoc($query);
                          if (isset($row['username'])){
                          $creator = $row['username'];
                          }
                        }
                        if($type=='video'){
                          echo "<span style= 'display: inline-block;'>
										        <video src='".$location."' controls width='200px'>This video could not be displayed :/</video>
										        <br>
										        <span><a href='media_content.php?mediaID='".$mediaID."''>".$name."</a></span>
									          </span>";
                        }elseif($type=='audio'){
                          echo "<span style= 'display: inline-block;'>
										        <audio src='".$location."' controls type='audio/mpeg'>This audio could not be displayed :/</audio>
										        <br>
										        <span><a href='media_content.php?mediaID='".$mediaID."''>".$name."</a></span>
								          	</span>";
                        }elseif($type=='image'){
                          echo "<span style= 'display: inline-block;'>
										        <img src='".$location."' width='200' alt='This image could not be displayed :/'/>
										        <br>
										        <span><a href='media_content.php?mediaID=".$mediaID."'>".$name."</a></span>
									          </span>";
                        }
                      }
                    }
                  }
                }while($row);
              }


            ?>
            </section>
        </main>
    </body>
</html>
