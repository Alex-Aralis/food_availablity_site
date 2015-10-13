<div class="headercontent">
    <div class="title"><i>flex</i> Demo</div>
    <div class="navbar">    
        <div class="navbaritem" openupgroup="main" openupshutter="ou1"> Link 1 </div>
        <div class="navbaritem" openupgroup="main" openupshutter="ou2"> Link 2 </div>
        <div class="navbaritem" openupgroup="main" openupshutter="/"> Files </div>
        <div class="navbaritem" openupgroup="main" openupshutter="ou3"> a longer title </div>
        <div class="navbaritem" openupgroup="main" openupshutter="ou4"> Link 5 </div>
        <div class="navbaritem" openupgroup="main" openupshutter="ou5"> Link 6 </div>
    </div>
</div>

<div class="openuplimiter">
    <div class="openupgroup" name="main">
        <div class="openupshutter" name="ou3">
            <div class="openupcontent">
                Some <br>
                openup <br>
                content.
            </div>
        </div>

        <?php include '/srv/http/food-availability-site/php/files_openup.php'; ?>

        <div class="openupshutter" name="ou5">
            <div class="openupcontent">
                <div class="navbar">
                    <div class="navbaritem" openupshutter="ou5/ou1" openupgroup='ou5/'> ou5/ou1 </div>
                    <div class="navbaritem" openupshutter="ou5/ou2"> ou5/ou2 </div>
                    <div class="navbaritem" openupshutter="ou5/ou3"> ou5/ou3 </div>
                </div>
            </div>        
        </div>        

                <div class="shadowbox openupgroup" name='ou5/'> 
                    <div class="openupshutter" name="ou5/ou1">
                        <div class="openupcontent">
                            ou5/ou1
                        </div>
                    </div>
 
                    <div class="openupshutter" name="ou5/ou2">
                        <div class="openupcontent">
                            ou5/ou2
                        </div>
                    </div>
                <div class="openuptopshadow"></div>
                <div class="openupgrouppadding">
                    <div class="openuppaddingshadow"></div>
                </div>
                </div>

        <div class="openuptopshadow"></div>
        <div class="openupgrouppadding">
           <div class="openuppaddingshadow"></div>
    </div>
</div>
</div>
