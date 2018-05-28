<?php
/**
* Editor.md 插件嵌入页
*
* Editor.md for Z-BlogPHP
*
* @author      心扬<https://app.zblogcn.com/?auth=2ffbff0a-1207-4362-89fb-d9a780125e0a>
* @version     2.7
*/

// Composer Autoload
require __DIR__ . '/vendor/autoload.php';

use League\HTMLToMarkdown\HtmlConverter;

// 注册插件
RegisterPlugin("Editormd", "ActivePlugin_Editormd");

/**
 *  挂载系统接口
 */
function ActivePlugin_Editormd()
{
    global $zbp; //使用变量$zbp前应进行全局声明

    /*
    * 检查编辑器冲突
    * 若冲突的插件已启用，则输出警告，并终止插件
    * 目前已知冲突的插件(ID)有：Neditor, UEditor，markdown，KindEditor，my_tinymce
    */
    if ($zbp->CheckPlugin('Neditor') || $zbp->CheckPlugin('UEditor')
     || $zbp->CheckPlugin('markdown') || $zbp->CheckPlugin('KindEditor')
      || $zbp->CheckPlugin('my_tinymce')) {
        Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'Editor_Conflict_Warning_Editormd');
        return;
    }

    Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'Edit_Head_Editormd'); //接口：文章编辑页加载前处理内容，输出位置在<head>尾部
    Add_Filter_Plugin('Filter_Plugin_Edit_End', 'Edit_Body_Editormd'); //接口：文章编辑页加载前处理内容，输出位置在<body>尾部
    Add_Filter_Plugin('Filter_Plugin_Edit_Response', 'Response1_Editormd'); //1号输出接口，在内容文本框下方，用于存放Editor.md 转换的 HTML 源码，以及加载提示
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'Extra_Support_Editormd'); //处理文章页模板接口
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Core','Post_Data_Editormd'); //接口：提交文章数据接管
    Add_Filter_Plugin('Filter_Plugin_PostPage_Core','Post_Data_Editormd'); //接口：提交文章数据接管
}


/**
 * 编辑器冲突警告
 * 显示在后台页面最顶部
 */
function Editor_Conflict_Warning_Editormd()
{
    global $zbp;

    echo '<h2 style="text-align:center;color:red;">Editor.md提示您：编辑器发生冲突！请在插件中心关闭其他编辑器！</h2>'; //输出警告
}

/**
 * 文章编辑页面<head>尾部插入
 * 用于引入Editor.md Script文件
 */
function Edit_Head_Editormd()
{
    global $zbp;

    echo '<link rel="stylesheet" type="text/css" href="' . $zbp->host . 'zb_users/plugin/Editormd/css/editormd.min.css">'; //Editor.md样式表
    echo '<script type="text/javascript" charset="utf-8" src="' . $zbp->host . 'zb_users/plugin/Editormd/editormd.min.js"></script>'; //Editor.md主体文件
}

/**
 * 文章编辑页面<body>尾部插入
 * 用于配置和启动 Editor.md
 */
function Edit_Body_Editormd()
{
    global $zbp;

    $plugin_url = $zbp->host . 'zb_users/plugin/Editormd';

    if (isset($_GET['id'])) {
        $act = 1;

        $article = new Post;
        $article->LoadInfoByID((integer) $_GET["id"]);

        if (is_null($article->Metas->md_content)) {
            $converter = new HtmlConverter(); //HTML To Markdown for PHP
            $md_content = json_encode($converter->convert($article->Content));
            $md_intro   = json_encode($converter->convert($article->Intro));
        } else {
            $md_content = json_encode($article->Metas->md_content);
            $md_intro   = json_encode($article->Metas->md_intro);
        }
    } else {
        $act = 0;
        $hasmd = 0;
        $md_content = '';
        $md_intro = '';
    }

    // 自定义配置项
    $toolbartheme = $zbp->Config('Editormd')->toolbartheme; // 工具栏主题设置
    $editortheme  = $zbp->Config('Editormd')->editortheme;  // 编辑区主题设置
    $previewtheme = $zbp->Config('Editormd')->previewtheme; // 预览区主题设置
    $preview      = $zbp->Config('Editormd')->preview;      // 实时预览设置
    $dynamictheme = $zbp->Config('Editormd')->dynamictheme; // 动态主题
    $autoheight   = $zbp->Config('Editormd')->autoheight;   // 编辑器自动长高
    $scrolling    = $zbp->Config('Editormd')->scrolling;    // 编辑器滚动
    $emoji        = $zbp->Config('Editormd')->emoji;        //  emoji
    if ($zbp->Config('Editormd')->htmldecode == 'true') {
        $htmlDecode = 'htmlDecode: true,';
    } elseif ($zbp->Config('Editormd')->htmldecode == 'filter') {
        $htmlDecode = 'htmlDecode: "' . $zbp->Config('Editormd')->htmlfilter . '",';
    } else {
        $htmlDecode = 'htmlDecode: false,';
    }
    if ($zbp->Config('Editormd')->extras == 'true') {
        $tocm         = $zbp->Config('Editormd')->tocm;        //  tocm列表设置
        $tasklist     = $zbp->Config('Editormd')->tasklist;    //  GFM 任务列表设置
        $flowchart    = $zbp->Config('Editormd')->flowchart;   // 流程图设置
        $katex        = $zbp->Config('Editormd')->katex;       //  Tex 科学公式语言设置
        $sdiagram     = $zbp->Config('Editormd')->sdiagram;    // 时序图/序列图设置
    } else {
        $tocm         = 'false';
        $tasklist     = 'false';   //  GFM 任务列表设置
        $flowchart    = 'false';   // 流程图设置
        $katex        = 'false';   //  Tex 科学公式语言设置
        $sdiagram     = 'false';   // 时序图/序列图设置
    }
    $texurl       = $zbp->Config('Editormd')->texurl; // Tex路径

    if ($scrolling == 'single')
        $scrolling = '"single"';

    // 动态主题js函数
    if ($dynamictheme == 'true') {
        $dynamicfunction = '
function themeSelect(id, themes, lsKey, callback)
{
    var select = $("#" + id);

    for (var i = 0, len = themes.length; i < len; i ++) {
        var theme    = themes[i];
        var selected = (localStorage[lsKey] == theme) ? " selected=\"selected\"" : "";

        select.append("<option value=\"" + theme + "\"" + selected + ">" + theme + "</option>");
    }

    select.bind("change", function(){
         var theme = $(this).val();

        if (theme === ""){
            alert("theme == \"\"");
            return false;
        }

        localStorage[lsKey] = theme;
        callback(select, theme);
    });

    return select;
}
        ';
        $themeconfig = 'theme : (localStorage.theme) ? localStorage.theme : "' . $toolbartheme .
                        '",previewTheme : (localStorage.previewTheme) ? localStorage.previewTheme : "' . $previewtheme .
                        '",editorTheme : (localStorage.editorTheme) ? localStorage.editorTheme : "' . $editortheme . '"';
        $select = '$("span#msg").html(\'<span id="theme-select">动态主题：<select id="editormd-theme-select"><option selected="selected" value="">选择工具栏主题</option></select>&emsp;<select id="editor-area-theme-select"><option selected="selected" value="">选择编辑器主题</option></select>&emsp;<select id="preview-area-theme-select"><option selected="selected" value="">选择实时预览主题</option></select></span><a href="' . $plugin_url . '/main.php#tabs=setting" style="border:solid 1px rgb(221,221,221);padding:4px 10px;margin-left:15px;">设 置</a>\');';
        $themeSelect = 'themeSelect("editormd-theme-select", editormd.themes, "theme", function($this, theme){
        ContentEditor.setTheme(theme);
        IntroEditor.setTheme(theme);
    });

    themeSelect("editor-area-theme-select", editormd.editorThemes, "editorTheme", function($this, theme) {
        ContentEditor.setCodeMirrorTheme(theme);
        IntroEditor.setCodeMirrorTheme(theme);
        // or ContentEditor.setEditorTheme(theme);
    });

    themeSelect("preview-area-theme-select", editormd.previewThemes, "previewTheme", function($this, theme) {
        ContentEditor.setPreviewTheme(theme);
        IntroEditor.setPreviewTheme(theme);
    });';
    } else {
        $dynamicfunction = '';
        $themeconfig = 'theme : "' . $toolbartheme .
                        '",previewTheme : "' . $previewtheme .
                        '",editorTheme : "' . $editortheme . '"';
        $select = '$("span#msg").html(\'<a href="' . $plugin_url . '/main.php#tabs=setting" style="border:solid 1px rgb(221,221,221);padding:4px 10px;position:relative;bottom:5px;float:right;">设 置</a>\');';
        $themeSelect = '';
    }

    /**
     * Heredoc
     * 预处理数据和界面，判断是否存在摘要
     * 定义并启动编辑器
     */
    $script = <<<EOF
<script type="text/javascript" charset="utf-8">
var has_intro = false, md_intro = '$md_intro';
var ContentEditor, IntroEditor;

//动态主题函数
$dynamicfunction

$(function() {
    if($act==1){//编辑内容
        $('textarea#editor_content').val($md_content);
        $('textarea#editor_intro').val($md_intro);
        if(md_intro.length==0 || md_intro.indexOf('<!--autointro-->')>-1){//摘要为空或者自动生成的
            $('#insertintro').html('○ 正文中首条「横线」以上的内容将自动作为摘要。您也可以点击[<span id="autointro">手动编辑摘要</span>]');
        } else {//摘要不为空
            $('#insertintro').html('○ 下面是原文章中已经存在的摘要。您也可以[<span id="autointro">重新生成摘要</span>]');
            has_intro = true;
        }//不考虑全是空格的情况
    } else {//新建内容
        $('#insertintro').html('○ 正文中首条「横线」以上的内容将自动作为摘要。您也可以[<span id="autointro">手动编辑摘要</span>]');
    }

    localStorage['theme']        = "$toolbartheme";
    localStorage['editorTheme']  = "$editortheme";
    localStorage['previewTheme'] = "$previewtheme";

    //编辑器上方动态主题下拉选择框
    $select

    // 自定义 Emoji 的 url 路径
    editormd.emoji = {
        path : "$plugin_url/images/github-emojis/",
        ext  : ".png"
    };

    // 自定义 Katex 地址
    editormd.katexURL = {
        js  : "$texurl",
        css : "$texurl"
    };

    // 内容编辑器
    ContentEditor = editormd("carea", {
        width: "100%",
        height: 640,
        path : '$plugin_url/lib/',
        $themeconfig,
        codeFold : true,
        syncScrolling : $scrolling,
        saveHTMLToTextarea : true,    // 保存 HTML 到 Textarea
        searchReplace : true,
        autoHeight : $autoheight,
        watch : $preview,  // 实时预览
        $htmlDecode // HTML 标签解析
        emoji : $emoji,
        taskList : $tasklist,  // Github Flavored Markdown 任务列表
        toc : $tocm,
        tocm : $tocm,         // Using [TOCM]
        tex : $katex,                   // 科学公式TeX语言支持，默认关闭
        flowChart : $flowchart,             // 流程图支持，默认关闭
        sequenceDiagram : $sdiagram,       // 时序/序列图支持，默认关闭,
        imageUpload : true,
        imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
        imageUploadURL : "$plugin_url/php/upload.php",
        crossDomainUpload : false,
        onload : function() {
            $('#emloading').hide();
            content_editor_init(this);
            if(has_intro){
                IntroEditor = editormd("tarea", {
                    width: "100%",
                    height: 300,
                    path : '$plugin_url/lib/',
                    saveHTMLToTextarea : true,
                    toolbarIcons : ["undo", "redo", "|", "bold", "del", "italic", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "|","watch"],
                    onload : function() {
                        intro_editor_init(this);
                    }
                });
            }
        }
    });

    $themeSelect

    //重新生成摘要
    $('span#autointro').click(function(){
        if($("#divIntro").is(":hidden")) {
            $("#divIntro").show();
            $('html,body').animate({scrollTop:$('#divIntro').offset().top},'fast');
        }
        IntroEditor = editormd("tarea", {
            width: "100%",
            height: 300,
            path : '$plugin_url/lib/',
            saveHTMLToTextarea : true,
            toolbarIcons : ["undo", "redo", "|", "bold", "del", "italic", "|", "h1", "h2", "h3", "h4", "h5", "h6", "|", "list-ul", "list-ol", "|","watch"],
            onload : function() {
                intro_editor_init(this);
                var s=ContentEditor.getValue();
                var hr_index = s.indexOf("------------");
                if(hr_index==-1)//没有横线
                    hr_index = 256;//截取256个字符
                this.setValue(s.substr(0, hr_index));
            }
        });
    });

    $('#carea, #tarea').css('z-index', 100);

    //保存 HTML 源码
    $("form#edit").submit(function(e){
        $("textarea[name='carea-html-code']").val(ContentEditor.getHTML());
        if(IntroEditor!=undefined){
            $("textarea[name='tarea-html-code']").val(IntroEditor.getHTML());
        }
        //event.preventDefault();
    });

    // 内容编辑器初始化, 用于支持 editor_api
    content_editor_init = function(obj){
        editor_api.editor.content.obj=obj;//内容编辑器对象
        //内容编辑器api方法
        editor_api.editor.content.get=function(){return this.obj.getValue()};//获取编辑器所有内容
        editor_api.editor.content.put=function(str){return this.obj.setValue(str)};//设置编辑器的内容
        editor_api.editor.content.focus=function(){return this.obj.focus()};//让编辑器获得尾部焦点
        sContent=obj.getValue();
    }

    // 摘要编辑器初始化, 用于支持 editor_api
    intro_editor_init = function(obj){
        editor_api.editor.intro.obj=obj;//摘要编辑器对象
        //摘要编辑器api方法
        editor_api.editor.intro.get=function(){return this.obj.getValue()};
        editor_api.editor.intro.put=function(str){return this.obj.setValue(str)};
        editor_api.editor.intro.focus=function(){return this.obj.focus()};
        sIntro=obj.getValue();
    }
});
//重定义原有函数
function editor_init(){}
</script>
<script type="text/javascript" charset="utf-8" src="$plugin_url/plugins/paste-upload.js"></script>
<style type="text/css">
#divMain a, #divMain2 a {
    color: #666;
}
#divMain a:hover, #divMain2 a:hover {
    color: #666;
}
.editormd-html-textarea {
    display: none;
}
span#theme-select select {
    height: 29px;
}
span#autointro:hover {
    cursor: pointer;
    text-decoration: underline;
}
</style>
EOF;

    echo $script;
}

/**
 * 在内容文本框下方插入，
 * 用于存放Editor.md 转换的 HTML 源码，
 * 以及加载提示
 */
function Response1_Editormd()
{
    echo '
    <textarea class="editormd-html-textarea" name="carea-html-code"></textarea>
    <textarea class="editormd-html-textarea" name="tarea-html-code"></textarea>
    <div id="emloading">
        <div style="font-size: 20px">Editormd 编辑器启动中……</div>
        <div style="color: #646464">如果这条消息一直显示，说明启动失败，请尝试:①刷新页面。或者：②删除 Editormd 插件再重新安装。如果仍有问题，请及时到论坛或应用中心反馈。</div>
    </div>';
}

/**
 * 前台扩展语言支持
 */
function Extra_Support_Editormd(&$template)
{
    global $zbp, $action, $mip_start;

    include __DIR__ . '/plugins/simple_html_dom.php'; // 引入simplehtmldom
    // 检查提交的文章内容是否包含<pre>
    $article = $template->GetTags('article');
    $html = str_get_html($article->Content);

    //兼容MIP，检测当前主题是否启用了官方MIP插件依赖
    if ($mip_start || $zbp->Config("Editormd")->mipsupport == 'true') {
        if (strpos($article, '[TOC]') !== FALSE || strpos($article, '[TOCM]') !== FALSE) {
            $titles = $html->find('h1,h2,h3,h4,h5,h6');
            $titles_html = '<div class="emd-toc"><div class="emd-toc-title">内容导航</div>';
            foreach ($titles as $title) {
                $titles_html .= '<div class="emd-toc-item emd-toc-h'. substr($title->tag, 1) .'"><a href="#'. trim($title->plaintext) .'">'. $title->plaintext .'</a></div>';
            }
            $titles_html .= '</div>';

            $zbp->header .= '<link rel="stylesheet" type="text/css" href="'. substr($zbp->host, 5) .'zb_users/plugin/Editormd/css/mipsupport.css">';//添加样式

            $article->Content = str_replace('[TOCM]', '', str_replace('[TOC]', '', $article->Content));

            $article->Content = $titles_html . $article->Content;
        }
        return;
    }

    $emoji     = $zbp->Config('Editormd')->emoji;       //  emoji
    $tocm      = $zbp->Config('Editormd')->tocm;        //  tocm列表设置
    $tasklist  = $zbp->Config('Editormd')->tasklist;    //  GFM 任务列表设置
    $flowchart = $zbp->Config('Editormd')->flowchart;   // 流程图设置
    $katex     = $zbp->Config('Editormd')->katex;       //  Tex 科学公式语言设置
    $texurl    = $zbp->Config('Editormd')->texurl;      // Tex路径
    $sdiagram  = $zbp->Config('Editormd')->sdiagram;    // 时序图/序列图设置

    if ($zbp->Config('Editormd')->htmldecode == 'true') {
        $htmlDecode = 'htmlDecode: true,';
    } elseif ($zbp->Config('Editormd')->htmldecode == 'filter') {
        $htmlDecode = 'htmlDecode: "' . $zbp->Config('Editormd')->htmlfilter . '",';
    } else {
        $htmlDecode = 'htmlDecode: false,';
    }

    //代码高亮
    if ($zbp->Config('Editormd')->extras == 'true' && !is_null($article->Metas->md_content)) {
        $zbp->header .= '<link rel="stylesheet" href="' . $zbp->host . 'zb_users/plugin/Editormd/css/editormd.preview.min.css">
        <style type="text/css">
            .editormd-html-preview {
                width: 100%;
                margin: 0;
                padding: 0;
            }
        </style>';
        $article->Content = str_replace('[TOC]', '', $article->Content);
        $article->Content = str_replace('[TOCM]', '', $article->Content);
        $article->Content = '<div id="html_content">'. $article->Content. '</div><div id="md_content"><textarea id="md_textarea" style="display:none;">'. $article->Metas->md_content . '</textarea></div>';

        $zbp->footer .= '<script src="' . $zbp->host . 'zb_users/plugin/Editormd/editormd.min.js"></script>
        <script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/marked.min.js"></script>
        <script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/prettify.min.js"></script>
        <script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/raphael.min.js"></script>';

        if ($sdiagram == 'true') {
            $zbp->footer .= '<script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/underscore.min.js"></script>
            <script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/sequence-diagram.min.js"></script>';
        }

        if ($flowchart == 'true') {
            $zbp->footer .= '<script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/flowchart.min.js"></script>
            <script src="' . $zbp->host . 'zb_users/plugin/Editormd/lib/jquery.flowchart.min.js"></script>';
        }

        $zbp->footer .= '<script type="text/javascript">
        editormd.emoji = {path : "' . $zbp->host . 'zb_users/plugin/Editormd/images/github-emojis/",ext  : ".png"};';
        if ($katex == 'true') {
            $zbp->footer .= 'editormd.katexURL = {js  : "' . $texurl . '",css : "' . $texurl . '"};';
        }
        $zbp->footer .= '$(function(){
            $("#html_content").hide();
            var EditormdView;
            EditormdView = editormd.markdownToHTML("md_content", {
                emoji           : ' . $emoji . ',
                toc             : ' . $tocm . ',
                tocm            : ' . $tocm . ',
                taskList        : ' . $tasklist . ',
                tex             : ' . $katex . ',
                flowChart       : ' . $flowchart . ',
                sequenceDiagram : ' . $sdiagram . ',
                ' . $htmlDecode . '
            });
        });</script>';
    } elseif ($zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE'] && $action != 'search' && !is_null($html->find('pre', 0))) {
        switch ($zbp->Config('Editormd')->codetheme) {
            case 'light_0':
                $codetheme = 'prettifylight';
                $linenums = 'ol.linenums,ol.linenums li{list-style:none !important;margin-left:0 !important;}';
                break;
            case 'light_1':
                $codetheme = 'prettifylight';
                $linenums = '';
                break;
            case 'dark_0':
                $codetheme = 'prettifymonokai';
                $linenums = 'pre code{background-color: none !important;}ol.linenums,ol.linenums li{list-style:none !important;margin-left:0 !important;}';
                break;
            case 'dark_1':
                $codetheme = 'prettifymonokai';
                $linenums = '';
                break;
            default:
                $codetheme = 'prettifylight';
                $linenums = '';
                break;
        }
        $zbp->header .= '<link rel=\'stylesheet\' href=\'' . $zbp->host . 'zb_users/plugin/Editormd/css/' . $codetheme . '.css\'><script src=\'' . $zbp->host . 'zb_users/plugin/Editormd/lib/prettify.min.js\'></script><style>' . $linenums . '</style>';
        $zbp->footer .=  '<script type="text/javascript">$(function(){ $("pre").addClass("prettyprint linenums"); prettyPrint(); });</script>';
    }
}

/**
 * 文章内容提交处理
 */
function Post_Data_Editormd(&$article)
{
    global $zbp;
    // 保存原始markdown数据至扩展元数据
    $article->Metas->md_content = $article->Content;
    $article->Metas->md_intro   = $article->Intro;

    $html = $_POST['carea-html-code']; //获取正文的HTML源码

    //兼容 MIP，检测
    if ($zbp->CheckPlugin('mip') || $zbp->Config("Editormd")->mipsupport == 'true') {
        include __DIR__ . '/plugins/simple_html_dom.php'; // 引入simplehtmldom
        $html = str_get_html($html);
        // 检查是否包含无href属性的<a>
        foreach ($html->find('a[!href]') as $link) {
            $link->href = '#';
        }
    }
    $article->Content = $html;

    if (empty($_POST['tarea-html-code'])) { //获取摘要的HTML源码
        $Parsedown = new Parsedown();
        $article->Intro = $Parsedown->text($article->Intro);
        // 将<!--autointro-->放到最后
        $article->Intro = str_replace('<!--autointro-->', '', $article->Intro);
        $article->Intro = $article->Intro . '<!--autointro-->';
    } else {
        $article->Intro = $_POST['tarea-html-code'];
    }

    $article->Save();
    return $article;
}

//插件安装激活时执行函数
function InstallPlugin_Editormd()
{
    global $zbp;

    // 初始化配置
    if (!$zbp->HasConfig('Editormd')) {
        $zbp->Config('Editormd')->toolbartheme = 'default'; // 工具栏主题设置
        $zbp->Config('Editormd')->editortheme  = 'default'; // 编辑区主题设置
        $zbp->Config('Editormd')->previewtheme = 'default'; // 预览区主题设置
        $zbp->Config('Editormd')->preview      = 'true';    // 实时预览
        $zbp->Config('Editormd')->autoheight   = 'false';   // 编辑器自动长高
        $zbp->Config('Editormd')->scrolling    = 'true';    // 编辑器滚动
        $zbp->Config('Editormd')->dynamictheme = 'true';    // 动态主题
        $zbp->Config('Editormd')->codetheme    = 'light_0'; // 前台代码主题
        $zbp->Config('Editormd')->emoji        = 'false';   // emoji 配置
        $zbp->Config('Editormd')->extras       = 'false';    // 扩展支持
        $zbp->Config('Editormd')->htmldecode   = 'false';    // HTML 解析
        $zbp->Config('Editormd')->htmlfilter   = 'style,script,iframe|on*';    // HTML 解析过滤标签
        $zbp->Config('Editormd')->tocm         = 'false';    // TOCM 列表
        $zbp->Config('Editormd')->tasklist     = 'false';    // GFM 任务列表
        $zbp->Config('Editormd')->flowchart    = 'false';    // 流程图
        $zbp->Config('Editormd')->katex        = 'false';    // Tex 科学公式语言
        $zbp->Config('Editormd')->texurl       = $zbp->host . 'zb_users/plugin/Editormd/lib/katex/katex.min';     // Katex路径
        $zbp->Config('Editormd')->sdiagram     = 'false';    // 时序图/序列图
        $zbp->Config('Editormd')->mipsupport   = 'false';    // 兼容第三方 MIP 主题
    }
    
    if (!file_exists(__DIR__ . '/images/github-emojis') || !file_exists(__DIR__ . '/lib/katex'))
        $zbp->Config('Editormd')->getpack      = 'true';    // 获取扩展包
    else
        $zbp->Config('Editormd')->getpack      = 'false';    // 不获取扩展包
    //$zbp->Config('Editormd')->keepmeta     = 1;       // 默认保存扩展元数据

    $zbp->SaveConfig('Editormd');
}

//插件卸载时执行函数
function UninstallPlugin_Editormd()
{
    global $zbp;

    // 删除配置
    /* 删除扩展元数据
    if (!$zbp->Config('Editormd')->keepmeta) {
        $article = new Post;
        $article->Metas->Del('md_content');
        $article->Metas->Del('md_intro');
    }*/
    $zbp->DelConfig('Editormd');
}
