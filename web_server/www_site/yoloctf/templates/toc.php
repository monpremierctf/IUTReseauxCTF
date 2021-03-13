
            <?php
            require_once(__SITEROOT__.'/ctf_utils/ctf_challenges.php');
            require_once(__SITEROOT__.'/ctf_utils/ctf_env.php');

            //
            // TOC Functions
            function print_toc_entry($cat){
                print '<a class="menu_2_href" href="index.php?p='.$cat.'">';
                print "<div class='menu_2 ctf-menu-color'>- ".getCategoryLabel($cat)."</div>";
                print '</a> ';
            }
            function getCategoriesInThema($theme){
                global $intros;
                $categories = array();
                foreach ($intros['results'] as $c) {
                    if (isset($c['theme'])) {
                        if ($c['theme']===$theme) {
                            if (!in_array($c['category'], $categories)) {
                                $categories[] = $c['category'];
                            }   
                        }               
                    }
                }
                return $categories;
            }

            function htmlMenu_1($category, $title, $hidden){
                # Curent page is in menu ?
                foreach(getCategoriesInThema($category) as $cat){
                    if ($cat==$_GET['p']) { $hidden=false;}
                }

                # HTML Menu
                print '<a onclick="ctf_toggle_hide(\'#menu_'.$category.'\')">
                            <div class="menu_1">'.$title.'</div> </a> ';
                print '<div id="menu_'.$category.'" ';
                if ($hidden) { print 'style="display:none;" ';}
                print ' >';    
                foreach(getCategoriesInThema($category) as $cat){
                    print_toc_entry($cat, $hidden);
                }
                print '</div>';
            }


            function htmlMenu_tool($href, $newpage, $title) {
                print '<a href="'.$href.'" ';
                if ($newpage) {
                    print 'target="_blank"';
                }
                print '><div class="menu_tools">['.$title.']</div></a> ';
            }
            //
            // Print TOC
            //$themes = ['Training', 'Vuln', 'Challs', '', 'Boxes'];
            print '<a class="menu_2_href"  href="index.php"><div class="menu_2 ctf-menu-color">Welcome</div></a>';   
            print '<p  ></p> ';
            htmlMenu_1("Intro", "Découverte", false);
            htmlMenu_1("Training", "Entrainement", true);
            htmlMenu_1("Vuln", "Failles historiques", true);
            htmlMenu_1("Challs", "Challenges", true);
            htmlMenu_1("Boxes", "Boxes", true);
                
            //
            // Tools
            //print '<a  ><pre> </pre></a> ';
            print '<p  ></p> ';
            htmlMenu_tool("index.php?p=Xterm", True, "Mon terminal");
            htmlMenu_tool("index.php?p=Proxy", True, "Mon proxy");
            htmlMenu_tool("index.php?p=Python", True, "Mon Python");
            //htmlMenu_tool("https://gui02.yoloctf.org/guacamole/", True, "Ma Kali");
            //htmlMenu_tool("index.php?p=Acces", True, "Mon accès");
            htmlMenu_tool("index.php?p=Scoreboard", True, "Score board");
            htmlMenu_tool("index.php?p=Profile", True, "Mon Compte");
            htmlMenu_tool("index.php?p=Feedback", True, "Feedback");
            htmlMenu_tool("/hackersguide/fr/", True, "Hacker Guide");
            //htmlMenu_tool("index.php?p=VM", True, "VMs");

/*
            print '<a href="index.php?p=Proxy"      target="_blank"><div class="menu_tools ctf-menu-color">[Mon proxy]</div></a> ';
            print '<a href="index.php?p=Python"     target="_blank"><div class="menu_tools ctf-menu-color">[Mon Python]</div></a> ';
            print '<a href="https://gui02.yoloctf.org/guacamole/"  target="_blank"><div class="menu_tools ctf-menu-color">[Ma Kali]</div></a> ';
            
            print '<a href="index.php?p=Acces"><div class="menu_tools ctf-menu-color">[Mon accès]</div></a> ';
            print '<a href="index.php?p=Scoreboard" target="_blank"><div class="menu_tools ctf-menu-color">[Score board]</div></a> ';
            print '<a href="index.php?p=Profile"    target="_blank"><div class="menu_tools ctf-menu-color">[Mon Compte]</div></a> ';
            print '<a href="index.php?p=Feedback"   target="_blank"><div class="menu_tools ctf-menu-color">[Feedback]</div></a> ';
            print '<a href="toolbox/toolbox.php"    target="_blank"><div class="menu_tools ctf-menu-color">[Hacker Guide]</div></a> ';
            print '<a href="index.php?p=VM"         target="_blank"><div class="menu_tools ctf-menu-color">[VMs]</div></a> ';
            */
            //
            // Admin tools
            if (isset($_SESSION['login'] )) {
                if (($_SESSION['login']==$admin  )) {
                    print '<a href="index.php?p=Infra"><pre class="menu_tools ctf-menu-color">[Infra]</pre></a> ';
                    print '<a href="index.php?p=Monitor"><pre class="menu_tools ctf-menu-color">[Monitor]</pre></a> ';
                    print '<a href="index.php?p=Zen"><pre class="menu_tools ctf-menu-color">[Admin]</pre></a> ';
                    print '<a href="index.php?p=AdminLog"><pre class="menu_tools ctf-menu-color">[Logs]</pre></a> ';
                    print '<a href="index.php?p=ChallServers"><pre class="menu_tools ctf-menu-color">[Chall Servers]</pre></a> ';
                    print '<a href="index.php?p=Editor"     target="_blank"><pre class="menu_tools ctf-menu-color">[Editeur]</pre></a> ';
                }
            }
            ?>
