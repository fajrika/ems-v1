            <!-- top nav -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle">
                                <i class="fa fa-bars"></i>
                            </a>
                        </div>
                        <form id="form_change_jp" action="<?=site_url()?>/Core/changeJP" method="post" class="flex">
                            <?php
                                if($GLOBALS['jabatan']):
                            ?>
                                <select class="btn btn-default" name="jabatan" id="jabatan" disabled>
                                    <option value="<?=$GLOBALS['jabatan']->id?>"><?=strtoupper($GLOBALS['jabatan']->name)?></option>
                                </select>
                                <select class="btn btn-default" name="project" id="project" disabled>
                                    <option value="<?=$GLOBALS['project']->id?>"><?=strtoupper($GLOBALS['project']->name)?></option>
                                </select>
                                <input id="changeJP" class="btn btn-default" type="button" value="Change" style="width: 75px; ">
                                <input id="saveJP" class="btn btn-default" type="button" value="Save" style="width: 75px; display:none">
                                <input id="cancel_changeJP" class="btn btn-danger" type="button" value="Cancel" style="width: 75px;display:none">
                                <input id="current-link"  name="current-link" class="btn btn-default col-md-3" type="hidden" style="width: 75px; display:none">
                            <?php
                                else:
                            ?>
                                <select class="btn btn-default" name="jabatan" id="jabatan" disabled>
                                    <option>SuperAdmin</option>
                                </select>
                            <?php endif; ?>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="<?=base_url()?>images/user.png" alt=""><?=ucwords($this->session->userdata('name'))?>
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li>
                                        <a href="javascript:;"> Profile</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <span class="badge bg-red pull-right">50%</span>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">Help</a>
                                    </li>
                                    <li>
                                        <a href="<?=site_url()?>/login/logout">
                                            <i class="fa fa-sign-out pull-right"></i> Log Out
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li role="presentation" class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-envelope-o"></i>
                                    <!-- <span class="badge bg-green">6</span> -->
                                </a>
                                <!-- <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                                    <li>
                                        <a>
                                            <span class="image"><img src="<?=base_url()?>images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span class="image"><img src="<?=base_url()?>images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                    <a>
                                            <span class="image"><img src="<?=base_url()?>images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a>
                                            <span class="image"><img src="<?=base_url()?>images/user.png" alt="Profile Image"></span>
                                            <span>
                                                <span>John Smith</span>
                                                <span class="time">3 mins ago</span>
                                            </span>
                                            <span class="message">
                                                Film festivals used to be do-or-die moments for movie makers. They were where...
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="text-center">
                                            <a>
                                                <strong>See All Alerts</strong>
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </div>
                                    </li>
                                </ul> -->
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top nav -->
        </div>
        
