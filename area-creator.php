<?php 
require_once('connect.php');

//check if form posted
if(isset($_POST['submit'])){
    
    if($_FILES['file1']['tmp_name']){
        //convert uploaded file to base64 encoded binary data
        $picture_data = file_get_contents($_FILES['file1']['tmp_name']);
        $picture_data = base64_encode($picture_data);   
    }
    
    //insert query- to feed posted data
    $insertQuery = "INSERT INTO `data` (`device_id`, `temperature`, `humidity`, `acidity`, `soil_type_id`, `nitrogen`, `light_freq_1`, `light_freq_2`, `light_freq_3`, `light_freq_4`, `light_freq_5`, `light_freq_6`, `light_freq_7`, `light_freq_8`, `data_layer`, `picture`) VALUES 
    ('".$mysqli->real_escape_string($_POST['device_id'])."',
     '".$mysqli->real_escape_string($_POST['temperature'])."',
     '".$mysqli->real_escape_string($_POST['humidity'])."',
     '".$mysqli->real_escape_string($_POST['acidity'])."',
     '".$mysqli->real_escape_string($_POST['soil_type'])."',
     '".$mysqli->real_escape_string($_POST['nitrogen'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_1'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_2'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_3'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_4'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_5'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_6'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_7'])."',
     '".$mysqli->real_escape_string($_POST['light_freq_8'])."',
     '".$_POST['data_layer']."', '".$picture_data."');";
    
    $result = $mysqli->query($insertQuery);//run created query to feed data into mysql database
    if(!$result){
        die('Please contact your developer !! <br><br>'.$mysqli->error.' '.$insertQuery);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<title> Soil Project:: Add an area detail </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css"  href="css/smart-forms.css">
    <link rel="stylesheet" type="text/css"  href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css"  href="css/as.css">

    <!--[if lte IE 9]>
    	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>    
        <script type="text/javascript" src="js/jquery.placeholder.min.js"></script>
    <![endif]-->    
    
    <!--[if lte IE 8]>
        <link type="text/css" rel="stylesheet" href="css/smart-forms-ie8.css">
    <![endif]-->
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=drawing,places"></script>
    <script type="text/javascript" src="js/as.js"></script>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/plugins.js" type="text/javascript"></script>
    <script src="js/scripts.js" type="text/javascript"></script>
        
       
</head>

<body class="woodbg">

	<div class="smart-wrap">
    	<div class="smart-forms smart-container wrap-2">
    
            <div class="form-header header-primary" id="menu">
            	<h4><i class="fa fa-flask"></i><a href="index.php" class="site-title">Soil Project</a>:: Add an area detail</h4>
                <a class="toggle" href="#"><i class="fa fa-bars"></i>Menu</a>
    			<ul id="menuse">
    				<li><a href="index.php" class="">Home</a></li>
                    <li><a href="area-creator.php" class="">Add Area</a></li>
    			</ul>
            </div><!-- end .form-header section -->
   	    
            
            <form method="post" action="" id="form-ui" enctype="multipart/form-data" onsubmit="return validate();">
            	<div class="form-body">
                
                    <div class="spacer-b30">
                    	<div class="tagline"><span>::Select an area on Map:: </span></div><!-- .tagline -->
                    </div>
                    
                    <?php if($result){ ?>
                        <div class="section notification-close">
                        
                            <div class="notification alert-success spacer-t10">
                                <p>Area details have been successfully saved.</p>
                                <a href="javascript:void(0);" class="close-btn" onclick="document.getElementsByClassName('notification-close')[0].style.display='none';">&times;</a>                                  
                            </div><!-- end .notification section -->                                                                         
                                               
                        </div>
                    <?php } ?>
                    
                    <div class="section">
                        <div class="map-container">
                            <!-- map controls-->
                            <input id="pac-input" class="controls" type="text" placeholder="Search Box" value="" />
                            <div id="color-palette"></div>
                            <div id="panel">
                                <div>
                                <button type="button" id="delete-shape-button">Delete Selected Shape</button><button type="button" id="save-shape" onclick="setdata_layer(globalcolor,globalvertices);">Save</button>
                                </div>
                            </div>
                            <div id="addMap_canvas"></div>
                        </div><!-- end .map-container -->                    
                    </div><!-- end .section -->
                    
                    <div class="section">
                        
                            <div class="notification alert-info spacer-t10">
                                <p id="addMap_canvas_data">Map Data::<br /><br />Select an area!!</p>                      
                            </div><!-- end .notification section -->                                                                         
                                               
                        </div>
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>::Add Details for the area selected:: </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label class="field">
                                    <input required="true" type="text" name="temperature" id="temperature" class="gui-input" placeholder="Temperature">
                                </label>
                            </div><!-- end section -->
                            
                            <div class="section">
                                <label class="field">
                                    <input required="true" type="text" name="acidity" id="acidity" class="gui-input" placeholder="Acidity">
                                </label>
                            </div><!-- end section -->
                            
                            <div class="section">
                                <label class="field">
                                    <input required="true" type="text" name="nitrogen" id="nitrogen" class="gui-input" placeholder="Nitrogen">
                                </label>
                            </div><!-- end section -->
                        
                        </div><!-- end .colm6 section -->
                        
                        <div class="colm colm6">
                        
                            <div class="section">
                                <label class="field">
                                    <input required="true" type="text" name="humidity" id="humidity" class="gui-input" placeholder="Humidity">
                                </label>
                            </div><!-- end section -->
                            
                            <div class="section">
                                <label class="field select">
                                <?php
                                //fetch soil types from database
                                $qry="SELECT * FROM `soil_types`";
                                
                                $result = $mysqli->query($qry);//run created query to feed data into mysql database
                                if(!$result){
                                    echo('There is no soil type in the database.');
                                }else{
                                        ?>
                                    <select required="true" id="soil_type" name="soil_type">
                                        <option value="">Select soil type</option>
                                    <?php
                                    while($soilType=$result->fetch_assoc()){
                                        echo '<option value="'.$soilType['soil_type_id'].'">'.$soilType['soil_type_name'].'</option>';
                                    }
                                    ?>    
                                    <!--<select required="true" id="soil_type" name="soil_type">
                                        <option value="">Select soil type</option>
                                        <option value="1">alkali-rich soils</option>
                                        <option value="2">alluvial soils</option>
                                        <option value="3">andosol soils</option>
                                        <option value="4">volcanic soils</option>
                                        <option value="5">humid soils (trumaos)</option>
                                        <option value="6">rivers soils</option>
                                    </select>-->
                                    </select>
                                    <i class="arrow"></i>
                                <?php } ?>     
                                </label>  
                            </div><!-- end section -->
                            
                            <div class="section">
                                <label class="field select">
                                    <?php
                                //fetch devices available in the database
                                $qry="SELECT * FROM `device`";
                                
                                $result = $mysqli->query($qry);//run created query to feed data into mysql database
                                if(!$result){
                                    echo('There is no device added in the database.');
                                }else{
                                        ?>
                                    <select required="" id="device_id" name="device_id">
                                        <option value="">Select device name</option>
                                    <?php
                                    while($soilType=$result->fetch_assoc()){
                                        echo '<option value="'.$soilType['device_id'].'">'.$soilType['device_name'].'</option>';
                                    }
                                    ?>
                                    </select>
                                    <i class="arrow"></i>
                                <?php } ?>
                                    <!--<select required="" id="device_id" name="device_id">
                                        <option value="">Select device name</option>
                                        <option value="1">0192K1 root sampler</option>
                                        <option value="2">0194 soil surface sampler</option>
                                        <option value="3">0194 soil surface sampler</option>
                                    </select>-->
                                </label>  
                            </div><!-- end section -->                            
                        
                        </div><!-- end .colm6 section -->
                                                               
                    </div><!-- end .frm-row section --> 
                    
                    <div class="spacer-t40 spacer-b30">
                    	<div class="tagline"><span> Light Frequencies </span></div><!-- .tagline -->
                    </div>
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_1" id="light_freq_1" class="gui-input" placeholder="Light Freq 1">
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_2" id="light_freq_2" class="gui-input" placeholder="Light Freq 2">     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_3" id="light_freq_3" class="gui-input" placeholder="Light Freq 3">
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_4" id="light_freq_4" class="gui-input" placeholder="Light Freq 4">     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_5" id="light_freq_5" class="gui-input" placeholder="Light Freq 5">
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_6" id="light_freq_6" class="gui-input" placeholder="Light Freq 6">     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    <div class="frm-row">
                    
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_7" id="light_freq_7" class="gui-input" placeholder="Light Freq 7">
                            </label>  
                        </div><!-- end section -->                     
                        
                        <div class="section colm colm6">
                            <label class="field select">
                                <input required="true" type="text" name="light_freq_8" id="light_freq_8" class="gui-input" placeholder="Light Freq 8">     
                            </label>  
                        </div><!-- end section --> 
                    
                    </div><!-- end .frm-row section -->
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>Add some description for the area(Optional) </span></div><!-- .tagline -->
                    </div>                    
                    
                    <div class="section">
                    	<label class="field prepend-icon">
                        	<textarea class="gui-textarea" id="description" name="description" placeholder="Text area"></textarea>
                            <label for="description" class="field-icon"><i class="fa fa-comments"></i></label>
                            <span class="input-hint"> 
                            	<strong>Hint:</strong> Don't be negative or off topic! just be awesome... 
                            </span>   
                        </label>
                    </div><!-- end section --> 
                    
                    <div class="spacer-t40 spacer-b40">
                    	<div class="tagline"><span>Upload area Picture </span></div><!-- .tagline -->
                    </div>                    
                    
                    <div class="section">
                        <label class="field prepend-icon file">
                            <span class="button btn-primary"> Choose File </span>
                <input type="file" class="gui-file" name="file1" id="file1" onChange="document.getElementById('uploader1').value = this.value;">
                            <input type="text" class="gui-input" id="uploader1" placeholder="no file selected" readonly>
                            <label class="field-icon"><i class="fa fa-upload"></i></label>
                        </label>
                    </div><!-- end  section -->
                    
                    <div class="frm-row hidden">
                        <input type="hidden" name="data_layer" id="data_layer" />
                    </div><!-- end .frm-row section -->
                                                                                             
                </div><!-- end .form-body section -->
                <div class="form-footer">
                	<input type="submit" name="submit" id="submit" class="button btn-primary" value="Proceed to save">
                </div><!-- end .form-footer section -->
            </form>
            
        </div><!-- end .smart-forms section -->
    </div><!-- end .smart-wrap section -->
    
    <div></div><!-- end section -->

</body>
</html>
