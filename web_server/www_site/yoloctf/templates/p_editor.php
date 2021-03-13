<script>
    window.onload = function() {
        initChalllist();
    }

    function initChalllist() {
        $.get(
            "api/zen_data.php", {
                ChallengeCategoryIntros: 1
            },
            function(data) {
                table = editor_challlist_table_start();
                //alert(data);

                classement = data; //JSON.parse(data);
                count = 1;
                for (const entry of classement) {
                    table += editor_challlist_table_entry(count, entry);
                    count = count + 1;
                }
                table += editor_challlist_table_stop();
                document.getElementById('challCatList').innerHTML = table;
            }
        );
    }


    function escapeHtml(unsafe) {
        if (unsafe === null) return "";
        unsafe = unsafe.toString();
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    //
    // Categories
    //

    function editor_challlist_table_start() {
        return ' \
        <table class="table table-striped">\
        <thead>\
            <tr>\
            <th scope="col">#</th>\
            <th scope="col">Category</th>\
            <th scope="col">Label Fr</th>\
            <th scope="col">Label Eng</th>\
            <th scope="col">Theme</th>\
            <th scope="col">Actions</th>\
            </tr>\
        </thead>\
        <tbody >\
        ';
    }

    function editor_challlist_table_entry(count, entry) {
        return ' \
        <tr> \
            <th scope="row">' + count.toString() + '</th> \
            <td>' + escapeHtml(entry.category) + '</td> \
            <td>' + escapeHtml(entry.label) + '</td> \
            <td>' + escapeHtml(entry.label_en) + '</td> \
            <td>' + escapeHtml(entry.theme) + '</td> \
            <td><button type="submit" class="btn btn-primary" onclick="return selectCategory(\''+ escapeHtml(entry.category) + '\')">Select</button> \
        </tr>';
    }


    function editor_challlist_table_stop() {
        return ' \
    </tbody> \
    </table> \
    ';
    }

    //
    // Challenges
    //

    function editor_chall_table_start(data) {
        ret = '<table class="table table-striped"><thead><tr>';
        var keyNames = Object.keys(myObject);
        for (var propName in keyNames) {
            ret +='<th scope="col">'+propName+'</th>';
        }
        ret += '</tr></thead><tbody >';
        return ret;
    }

    function editor_chall_table_entry(count, entry) {
        return ' \
    <tr> \
        <th scope="row">' + count.toString() + '</th> \
        <td>' + escapeHtml(entry.login) + '</td> \
        <td>' + escapeHtml(entry.score) + '</td> \
        <td>' + escapeHtml(entry.etablissement) + '</td>  \
        <td>' + escapeHtml(entry.lycee) + '</td>  \
    </tr>';
    }


    function editor_chall_table_stop() {
        return ' \
    </tbody> \
    </table> \
    ';
    }


    var multilineattrib = ['description','description_en'];
    function dumpLineIntroAttrib(name,value) {
        if (multilineattrib.includes(name)) {
            ret = ' \
                <div class="form-group text-left  col-12" style="margin-bottom: 1px;">  \
                <label for="usr" class="col-2">'+name+'</label> \
                <input type="hidden" id="intro_'+name+'" name="mail_current" value="'+value+'"> \
                <textarea name="textarea" id="intro_'+name+'" style="width:80%;height:150px;">'+value+'</textarea> \
                </div> \
                ';
        } else {
            ret = ' \
                <div class="form-group text-left  col-12"  style="margin-bottom: 1px;">  \
                <label for="usr" class="col-2">'+name+'</label> \
                <input type="hidden" id="intro_'+name+'" name="mail_current" value="'+value+'"> \
                <input type="text"   id="intro_'+name+'" class="col-6" id="mail" name="mail" value="'+value+'"> \
                </div> \
                ';
        }
        return ret;
    }

    function selectCategory(category){
        $.get(
            "api/zen_data.php", {
                ChallengeCategoryIntro: category
            },
            function(data) {
                table = "<div class='row border border-success'>";
                /*
                Object.keys(data).forEach(key => {
                    table += dumpLineIntroAttrib(key,data[key]);
                })
                */
                var attrib = ['theme', 'category',  'label', 'label_en', 'dir', 'docker', 'description', 'description_en',  ] ; 
                attrib.forEach(key => {
                    table += "";
                    table += dumpLineIntroAttrib(key,data[key]);
                    table += "";  
                })      
                table += "</div>";    
                document.getElementById('challCatIntro').innerHTML = table;
            }

        );
        $.get(
            "api/zen_data.php", {
                ChallengeCategory: category
            },
            function(data) {
                /*
                table = editor_challlist_table_start();
                //alert(data);

                classement = data; //JSON.parse(data);
                count = 1;
                for (const entry of classement) {
                    table += editor_challlist_table_entry(count, entry);
                    count = count + 1;
                }
                table += editor_challlist_table_stop();
                document.getElementById('challCatList').innerHTML = table;
                */

                table = "";
                Object.keys(data).forEach(key => {
                    var attrib = ['id', 'name',  'name_en', 'label_en', 'value', 'requirements', 'state', 'docker', 'max_attempts', 'type', 'description', 'description_en',  ] ; 
                    table += "<div class='row border border-primary rounded'>";
                    attrib.forEach(challkey => {                        
                        table += dumpLineIntroAttrib(challkey,data[key][challkey]);                        
                    }) 
                    table += "</div><hr>"; 
                }) 
                table += ""; 
                document.getElementById('challCatChalls').innerHTML = table;
            }
        );
    }
</script>

<style>

.tableFixHead          { overflow-y: auto; height: 350px; }
/*.tableFixHead thead th { position: sticky; top: 0; }

.overFlowChallList {
  height:350px;
  overflow-y: scroll;
}
*/
</style>

<div class="col text-center">
    <div class="col text-left">
        <h2>Editeur de Challenges</h2><br><br>
    </div>
    <div class="col text-center">

        <!---- Challenge Categories List  --->
        <div class="">
            <div class="row chall-titre bg-secondary text-white">
                <div class="col-sm text-left">Catégories</div>
            </div>
            <div id="challCatList" class="form-group text-left row tableFixHead">

            </div>

            <div class="form-group text-right row ">
                <label for="usr" class="col-2"></label>
                <button type="submit" class="btn btn-primary" onclick="return onProfileSave()">New categorie</button>
            </div>
        </div>

        <!---- Categories Detail  --->

        <div class="">
            <div class="row chall-titre bg-secondary text-white">
                <div class="col-sm text-left">Catégorie - Détail</div>
            </div>
            <div class="text-left row ">
                <label for="usr" class="row">Intro</label>
                <div id="challCatIntro" class="form-group text-left row">   </div>
            </div>
            <div class="text-left row ">
                
            </div>
            <div class="text-left row ">
                <label for="usr" class="row">Challenges</label>
            </div>
            <div class="text-left row ">
                <div id="challCatChalls" class="form-group text-left row">  </div>
            </div>
        </div>
        <div class="form-group text-left  row ">
            <hr>
        </div>
    </div>
</div>