<footer>
            <?php
                $filename = 'index.php';
                $filename2 = 'comments.php';
                if(file_exists($filename)){
                    echo "Updated " . date ("l, F d Y h:ia", filemtime($filename));
                    }
                
                if(file_exists($filename2)){
                    echo "Updated " . date ("l, F d Y h:ia", filemtime($filename2));
                    }
            ?>
                <br><br>
                <a href="https://validator.w3.org/nu/?doc=http://solace.ist.rit.edu/~smc9253/240/Modular_Project/<?php echo $fildir.$filename;?>" target="_blank">
                    <img src="<?php echo $path;?>assets/img/html_5_logo_transparent.gif" width="45" height="59" alt="HTML5" title="HTML5">
                </a>

                &nbsp; &nbsp; 

                <a href="https://jigsaw.w3.org/css-validator/validator?uri=http://solace.ist.rit.edu/~smc9253/240/Modular_Project/<?php echo $fildir.$filename;?>" target="_blank">
                    <img src="<?php echo $path;?>assets/img/css_validator.png"  width="45" height="59" alt="CSS" title="CSS">
                </a>

                &nbsp; &nbsp;  
        </footer>
</body>
</html>