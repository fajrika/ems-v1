            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="clearfix"></div>
                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            <img src="<?=base_url()?>/images/user.png" alt="..." class="img-circle profile_img">
                        </div>
                        <div class="profile_info">
                            <span>
                                <?php
                                    $jabatan_name = $this->session->userdata('jabatan_name'); 
                                    echo($jabatan_name?strtoupper($jabatan_name):'-');
                                ?>   
                            </span>
                            <h2><?=ucwords($this->session->userdata('name'))?></h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br>

                    <!-- sidebar menu -->
                    
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <?php
                        foreach ($menu['menu_by_permission'] as $lvl1)
                        {
                            if ($lvl1['url']=="" && $lvl1['id_parent']=="")
                            {
                                echo '<div class="menu_section">';
                                    echo '<h3>'.$lvl1['name'].'</h3>';
                                    echo '<ul class="nav side-menu">';
                                    foreach ($menu['menu_by_permission'] as $lvl2)
                                    {
                                        if ($lvl1['id']==$lvl2['id_parent'])
                                        {
                                            if ($lvl2['url']=="")
                                            {
                                                echo '<li>';
                                                    echo '<a>';
                                                    echo '<i class="fa fa-database"></i> '.$lvl2['name'].' ';
                                                    echo '<span class="fa fa-chevron-down"></span>';
                                                    echo '</a>';
                                                    echo '<ul class="nav child_menu">';
                                                    foreach ($menu['menu_by_permission'] as $lvl3)
                                                    {
                                                        if ($lvl2['id']==$lvl3['id_parent'])
                                                        {
                                                            if ($lvl3['url']=="")
                                                            {
                                                                echo '<li>';
                                                                    echo '<a>';
                                                                    echo '<i></i> '.$lvl3['name'].' ';
                                                                    echo '<span class="fa fa-chevron-down"></span>';
                                                                    echo '</a>';
                                                                    echo '<ul class="nav child_menu">';
                                                                    foreach ($menu['menu_by_permission'] as $lvl4)
                                                                    {
                                                                        if ($lvl3['id']==$lvl4['id_parent'])
                                                                        {
                                                                            if ($lvl4['url']=="")
                                                                            {
                                                                                echo '<li>';
                                                                                    echo '<a>';
                                                                                    echo '<i></i> '.$lvl4['name'].' ';
                                                                                    echo '<span class="fa fa-chevron-down"></span>';
                                                                                    echo '</a>';
                                                                                    echo '<ul class="nav child_menu">';
                                                                                    foreach ($menu['menu_by_permission'] as $lvl5)
                                                                                    {
                                                                                        if ($lvl4['id']==$lvl5['id_parent'])
                                                                                        {
                                                                                            // if ($lvl5['url']=="")
                                                                                            // {
                                                                                            //     echo '<li>';
                                                                                            //         echo '<a>';
                                                                                            //         echo '<i></i> '.$lvl5['name'].' ';
                                                                                            //         echo '<span class="fa fa-chevron-down"></span>';
                                                                                            //         echo '</a>';
                                                                                            //         echo '<ul class="nav child_menu">';
                                                                                                        
                                                                                            //         echo '</ul>';
                                                                                            //     echo '</li>';
                                                                                            // }
                                                                                            // else
                                                                                            // {
                                                                                                echo '<li><a href="'.site_url()."".$lvl5['url'].'">'.$lvl5['name'].'</a></li>';
                                                                                            // }
                                                                                        }
                                                                                    }
                                                                                    echo '</ul>';
                                                                                echo '</li>';
                                                                            }
                                                                            else
                                                                            {
                                                                                echo '<li><a href="'.site_url()."".$lvl4['url'].'">'.$lvl4['name'].'</a></li>';
                                                                            }
                                                                        }
                                                                    }
                                                                    echo '</ul>';
                                                                echo '</li>';
                                                            }
                                                            else
                                                            {
                                                                echo '<li><a href="'.site_url()."".$lvl3['url'].'">'.$lvl3['name'].'</a></li>';
                                                            }
                                                        }
                                                    }
                                                    echo '</ul>';
                                                echo '</li>';
                                            }
                                            else
                                            {
                                                echo '<li><a href="'.site_url()."".$lvl2['url'].'">'.$lvl2['name'].'</a></li>';
                                            }
                                        }
                                    }
                                    echo '</ul>';
                                echo '</div>';
                            }
                        }
                    ?>
                    </div>
                <!-- /sidebar menu -->
                </div>
            </div>
            <!-- /menu footer buttons -->