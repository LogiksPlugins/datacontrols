<?php
if(!defined('ROOT')) exit('No direct script access allowed');

$slugs = _slug("a/dcmode/c/d");
if(!isset($slugs['dcmode']) || strlen($slugs['dcmode'])<=0) $slugs['dcmode'] = "reports";
$_REQUEST['panel'] = $slugs['dcmode'];

loadModule("pages");

function pageSidebar() {
    return "<div id='componentTree' class='componentTree list-group list-group-root'></div>";
}

function pageContentArea() {
    return "<div id='contentArea' class='table-responsive'><table class='table table-responsive'>
    <thead>
        <tr>
            <th width=100px>SL#</th>
            <th width=150px>Title</th>
            <th width=150px>Module</th>
            <th>Path</th>
            <th>Updated On</th>
            <th>--</th>
        </tr>
    </thead><tbody></tbody></table></div>";
}

$toolBar = [
        "refreshUI"=>["icon"=>"<i class='fa fa-refresh'></i>","tips"=>"Recache"],
        //"createDC"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Create"],
        //"cloneDC"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Clone"],
        //"exportDC"=>["icon"=>"<i class='fa fa-plus'></i>","tips"=>"Export"],
        ['type'=>"bar"],
        //"trashDC"=>["icon"=>"<i class='fa fa-trash'></i>","tips"=>"Delete"],
        
		//["title"=>"Search Store","type"=>"search","align"=>"right"],
        
        "panelReports"=>["title"=>"Reports","align"=>"right","class"=>($_REQUEST['panel']=="reports")?"active":""],    
		"panelForms"=>["title"=>"Forms","align"=>"right","class"=>($_REQUEST['panel']=="forms")?"active":""],
		"panelInfoviews"=>["title"=>"Infoviews","align"=>"right","class"=>($_REQUEST['panel']=="infoviews")?"active":""],
		"panelVisuals"=>["title"=>"Visuals","align"=>"right","class"=>($_REQUEST['panel']=="infovisuals")?"active":""],
		"panelViews"=>["title"=>"Views","align"=>"right","class"=>($_REQUEST['panel']=="views")?"active":""],
];

$moduleName = basename(dirname(__FILE__));

echo _css([$moduleName]);
echo _js($moduleName);

printPageComponent(false,[
    "toolbar"=>$toolBar,
    "sidebar"=>"pageSidebar",
    "contentArea"=>"pageContentArea"
  ]);
?>
<style>
.list-group-item {
    cursor: pointer;
}
</style>
<script>
var trTemplate = "<tr data-module='{{module}}' data-fpath='{{fpath}}'><th width=100px>{{nx}}</th><td class='name'>{{name}}</td><td>{{module}}</td><td>{{fpath}}</td><td>{{last_updated}}</td><td class='action'>{{{actions}}}</td></tr>";
var trSidebar = "<li class='list-group-item' data-module='{{mod}}'>{{name}}</li>";
$(function() {
    trTemplate = Handlebars.compile(trTemplate);
    trSidebar = Handlebars.compile(trSidebar);
    
    $("#componentTree").delegate(".list-group-item","click", function() {
        $("#componentTree li.active").removeClass("active");
        $(this).addClass("active");
        
        if($(this).data("module")=="" || $(this).data("module")=="*") {
            $("#contentArea tbody tr").show();
        } else {
            $("#contentArea tbody tr").hide();
            $("#contentArea tbody tr[data-module='"+$(this).data("module")+"']").show();
        }
    });
    $("#contentArea tbody").delegate(".actionBtn","click", function() {
        cmd = $(this).data("cmd");
        
        if(cmd=="editDC") editDC(this);
        else console.log("Not Supported Yet");
    });
    
    updateListTable()
});
function refreshUI() {
    //window.location.reload();
    updateListTable("true");
}
function panelReports() {
    window.location = _link("modules/<?=$moduleName?>/reports");
}
function panelForms() {
    window.location = _link("modules/<?=$moduleName?>/forms");
}
function panelInfoviews() {
    window.location = _link("modules/<?=$moduleName?>/infoviews");
}
function panelVisuals() {
    window.location = _link("modules/<?=$moduleName?>/infovisuals");
}
function panelViews() {
    window.location = _link("modules/<?=$moduleName?>/views");
}


function updateListTable(recache) {
    if(recache==null) recache = "false";
    
    $("#componentTree").html("");
    $("#contentArea tbody").html("<tr><th colspan=100><div class='ajaxloading ajaxloading5'></div></th></tr>");
    
    processAJAXQuery(_service("<?=$moduleName?>","list")+"&dcmode=<?=$_REQUEST['panel']?>&recache="+recache, function(data) {
        if(data.Data==null) {
            data.Data = "Error while listing the controls";
        }
        if(data.Data.modules!=null) {
            $("#componentTree").append("<li class='list-group-item active' data-module='*'>All Modules</li>");
            $.each(data.Data.modules, function(mod, name) {
                $("#componentTree").append(trSidebar({"mod":mod,"name":name}));
            });
        }
        
        if(data.Data.files!=null && Array.isArray(data.Data.files)) {
            if(data.Data.files.length>0) {
                $("#contentArea tbody").html("");
                
                $.each(data.Data.files, function(a, ctrl) {
                    ctrl['nx'] = $("#contentArea tbody").children().length+1;
                    ctrl['actions'] = getRowActions(ctrl);
                    $("#contentArea tbody").append(trTemplate(ctrl));
                });
            } else {
                $("#contentArea tbody").html("<tr><th colspan=100><h5 align=center>No Controls Found</h5></th></tr>");
            }
        } else {
            $("#contentArea tbody").html("<tr><th colspan=100><h5 align=center>"+(typeof data.Data)+"</h5></th></tr>");
        }
    },"json");
}
function getRowActions(ctrl) {
    html = [];
    html.push("<i class='actionBtn fa fa-pencil fa-2x pull-left float-left' data-path='"+ctrl.fpath+"' data-cmd='editDC'></i>");
    
    return html.join("");
}

function createDC(btn) {
    dcMode = "<?=$_REQUEST['panel']?>";
    //alert(dcMode);
}
function editDC(btn) {
    fpath = $(btn).closest("tr").data("fpath");
    name = $(btn).closest("tr").find("td.name").text();
    parent.openLinkFrame("<?=strtoupper($_REQUEST['panel'])?>:"+name,_link("modules/datacontrolsEditor/<?=$_REQUEST['panel']?>")+"&fpath="+fpath);
}
</script>