    <header>
        <a href="https://www.baltimorecountymd.gov/">
            <img src="<?php echo $path;?>assets/img/baltimorecounty.png" alt="Baltimore, MD" title="Baltimore, MD" width="100">
        </a>
        <ul id="myTopnav" class="topnav">
            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(0)">More</a>
                <div class="dropdown-content">
                    <a href="<?php echo $path;?>references/index.php">References</a>
                    <a href="<?php echo $path;?>grading/index.php">Grading</a>
                    <a href="<?php echo $path;?>about/index.php">About</a>
                </div>
            </li>

            <li><a href="<?php echo $path;?>comments/comments.php">Comments</a></li>
            <li><a href="<?php echo $path;?>feedback/index.php">Feedback</a></li>
            <li><a href="<?php echo $path;?>rest/index.php">Rest</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(1)">Food</a>
                <div class="dropdown-content">
                    <a href="<?php echo $path;?>woodberry/index.php">Woodberry Kitchen</a>
                    <a href="<?php echo $path;?>reserve/index.php">The Reserve Restaurant</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(2)">Activities</a>
                <div class="dropdown-content">
                    <a href="<?php echo $path;?>aquarium/index.php">National Aquarium</a>
                    <a href="<?php echo $path;?>zoo/index.php">Maryland Zoo</a>
                </div>
            </li>

            <li><a href="<?php echo $path;?>home/index.php">Home</a></li>

            <li><a href="javascript:void(0);" class="icon" onclick="menuHider()">&#9776;</a></li>
        </ul>
    </header>