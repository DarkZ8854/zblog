<?php
/**
* Editor.md 插件配置页
*
* Editor.md for Z-BlogPHP
*
* @author      心扬<https://app.zblogcn.com/?auth=2ffbff0a-1207-4362-89fb-d9a780125e0a>
* @version     2.7
*/

require __DIR__ . '/../../../zb_system/function/c_system_base.php';
require __DIR__ . '/../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('Editormd')) {
    $zbp->ShowError(48);
    die();
}

require __DIR__ . '/../../../zb_system/admin/admin_header.php';
require __DIR__ . '/../../../zb_system/admin/admin_top.php';

// 自定义配置项
$toolbartheme = $zbp->Config('Editormd')->toolbartheme; // 工具栏主题设置
$editortheme  = $zbp->Config('Editormd')->editortheme;  // 编辑区主题设置
$previewtheme = $zbp->Config('Editormd')->previewtheme; // 预览区主题设置
$preview      = $zbp->Config('Editormd')->preview;      // 实时预览设置
$autoheight   = $zbp->Config('Editormd')->autoheight;    // 编辑器自动长高
$scrolling    = $zbp->Config('Editormd')->scrolling;    // 编辑器滚动
$dynamictheme = $zbp->Config('Editormd')->dynamictheme; // 动态主题
$codetheme    = $zbp->Config('Editormd')->codetheme;    // 前台代码主题设置
$emoji        = $zbp->Config('Editormd')->emoji;        //  emoji设置
$htmldecode   = $zbp->Config('Editormd')->htmldecode;   // HTML 解析
$extras       = $zbp->Config('Editormd')->extras;       // 扩展支持设置
$tocm         = $zbp->Config('Editormd')->tocm;        //  tocm目录设置
$tasklist     = $zbp->Config('Editormd')->tasklist;    //  GFM 任务列表设置
$flowchart    = $zbp->Config('Editormd')->flowchart;   // 流程图设置
$katex        = $zbp->Config('Editormd')->katex;       //  Tex 科学公式语言设置
$sdiagram     = $zbp->Config('Editormd')->sdiagram;    // 时序图/序列图设置
$mipsupport   = $zbp->Config('Editormd')->mipsupport;  // 第三方MIP兼容设置
if ($htmldecode == 'filter')
    $htmlfilter = $zbp->Config('Editormd')->htmlfilter; // HTML 解析过滤标签

//判断下载扩展包
if ($zbp->Config('Editormd')->getpack == 'true' && (!file_exists(__DIR__ . '/images/github-emojis') || !file_exists(__DIR__ . '/lib/katex'))) {
    $getpack = true;
} else {
    $getpack = false;
}
?>

<style>
@import url('<?php echo $zbp->host; ?>zb_users/plugin/Editormd/css/editormd.min.css');
@import url('<?php echo $zbp->host; ?>zb_users/plugin/Editormd/css/layui/css/layui.css');
</style>

<div id="divMain">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
        <legend>Editor.md 配置</legend>
    </fieldset>

    <div class="layui-tab layui-tab-brief" lay-filter="tabs">
        <ul class="layui-tab-title" style="height:auto">
            <li lay-id="setting">功能设置</li>
            <li lay-id="preview">编辑器预览</li>
            <li lay-id="help">帮助说明</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item">
                <form action="" class="layui-form">
                    <fieldset class="layui-elem-field layui-field-title">
                        <legend>后台编辑器</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <label class="layui-form-label">动态主题</label>
                                <div class="layui-input-inline">
                                    <select name="dynamictheme" id="dynamictheme">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($dynamictheme=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($dynamictheme=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="dynamic-tip">&#xe60b;</i></div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">emoji 表情</label>
                                <div class="layui-input-inline">
                                    <select name="emoji" id="emoji">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($emoji=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($emoji=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="emoji-tip">&#xe60b;</i></div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">实时预览</label>
                                <div class="layui-input-inline">
                                    <select id="preview" name="preview">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($preview=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($preview=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="pre-tip">&#xe60b;</i></div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">自动长高</label>
                                <div class="layui-input-inline">
                                    <select name="autoheight" id="autoheight">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($autoheight=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($autoheight=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="ah-tip">&#xe60b;</i></div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">同步滚动</label>
                                <div class="layui-input-inline">
                                    <select name="scrolling" id="scrolling">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($scrolling=='true')echo 'selected'?>>双向</option>
                                        <option value="single" <?php if($scrolling=='single')echo 'selected'?>>单向</option>
                                        <option value="false" <?php if($scrolling=='false')echo 'selected'?>>禁用</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="scroll-tip">&#xe60b;</i></div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="layui-elem-field layui-field-title">
                        <legend>前台内容显示</legend>
                        <div class="layui-field-box">
                            <?php if($zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE']): ?>
                            <div class="layui-form-item">
                                <label class="layui-form-label">前台代码高亮</label>
                                <div class="layui-input-inline">
                                    <select id="editormd-codetheme-select" name="codetheme">
                                        <option value="">请选择</option>
                                        <option value="light_0" <?php if($codetheme=="light_0")echo 'selected'?>>明亮（不显示行数）</option>
                                        <option value="light_1" <?php if($codetheme=="light_1")echo 'selected'?>>明亮（显示行数）</option>
                                        <option value="dark_0" <?php if($codetheme=="dark_0")echo 'selected'?>>黑暗（不显示行数）</option>
                                        <option value="dark_1" <?php if($codetheme=="dark_1")echo 'selected'?>>黑暗（显示行数）</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="ct-tip">&#xe60b;</i></div>
                            </div>
                            <?php else: ?>
                            <div class="layui-form-item">
                                <label class="layui-form-label">前台代码高亮</label>
                                <div class="layui-input-inline">
                                    <div class="layui-form-mid layui-word-aux">“代码高亮”功能已关闭，请到“<a href="<?php echo $zbp->host; ?>zb_system/admin/index.php?act=SettingMng#tab2"> 网站设置 --> 全局设置 </a>”中开启</div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                    
                    <fieldset class="layui-elem-field layui-field-title">
                        <legend>扩展功能支持</legend>
                        <div class="layui-field-box">
                            <div class="layui-form-item">
                                <label class="layui-form-label">第三方MIP主题</label>
                                <div class="layui-input-inline">
                                    <select id="mipsupport" name="mipsupport">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($mipsupport=='true') echo 'selected="selected"'?>>开启兼容</option>
                                        <option value="false" <?php if($mipsupport=='false') echo 'selected="selected"'?>>关闭兼容</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="mip-tip">&#xe60b;</i></div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">HTML 解析</label>
                                <div class="layui-input-inline">
                                    <select id="htmldecode" name="htmldecode">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($htmldecode=='true') echo 'selected="selected"'?>>开启，全部解析</option>
                                        <option value="filter" <?php if($htmldecode=='filter') echo 'selected="selected"'?>>开启，过滤标签</option>
                                        <option value="false" <?php if($htmldecode=='false') echo 'selected="selected"'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="hd-tip">&#xe60b;</i></div>
                            </div>

                            <?php if($htmldecode=='filter'): ?>
                            <div class="layui-form-item">
                                <label class="layui-form-label">过滤标签</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="htmlfilter" placeholder="" autocomplete="off" class="layui-input" value="<?php echo $htmlfilter; ?>">
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="hf-tip">&#xe60b;</i> <i class="layui-icon" style="font-size:28px" id="hf-reset">&#x1002;</i></div>
                            </div>
                            <?php endif; ?>

                            <div class="layui-form-item">
                                <label class="layui-form-label">编辑器扩展</label>
                                <div class="layui-input-inline">
                                    <select id="extras" name="extras">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($extras=='true') echo 'selected="selected"'?>>开启</option>
                                        <option value="false" <?php if($extras=='false') echo 'selected="selected"'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="ext-tip">&#xe60b;</i></div>
                            </div>

                            <?php if($extras=='true'): ?>
                            <div class="layui-form-item">
                                <label class="layui-form-label">ToC/ToCM 目录</label>
                                <div class="layui-input-inline">
                                    <select id="tocm" name="tocm">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($tocm=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($tocm=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">GFM 任务列表</label>
                                <div class="layui-input-inline">
                                    <select id="tasklist" name="tasklist">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($tasklist=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($tasklist=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">流程图</label>
                                <div class="layui-input-inline">
                                    <select id="flowchart" name="flowchart">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($flowchart=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($flowchart=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">Tex 科学公式</label>
                                <div class="layui-input-inline">
                                    <select id="katex" name="katex">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($katex=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($katex=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                                <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="tex-tip">&#xe60b;</i></div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">时序图/序列图</label>
                                <div class="layui-input-inline">
                                    <select id="sdiagram" name="sdiagram">
                                        <option value="">请选择</option>
                                        <option value="true" <?php if($sdiagram=='true')echo 'selected'?>>开启</option>
                                        <option value="false" <?php if($sdiagram=='false')echo 'selected'?>>关闭</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                    
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <button class="layui-btn" lay-submit lay-filter="Setting">立即提交</button>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="setting">
                </form>
                <?php if ($zbp->Config('Editormd')->getpack == 'delay' || !file_exists(__DIR__ . '/images/github-emojis') || !file_exists(__DIR__ . '/lib/katex')): ?>
                <button class="layui-btn layui-btn-danger layui-btn-fluid" onclick="$.get('setting.php?action=getpack',function(r){location.reload()})">点击重新获取扩展包</button>
                <?php endif; ?>
            </div>
            <div class="layui-tab-item">
                <form action="" class="theme">
                    <div id="theme-select">
                        <label for="editormd-theme-select">
                            工具栏：
                        </label>
                        <select id="editormd-theme-select" name="toolbartheme">
                            <option selected="selected" value="">选择工具栏主题</option>
                        </select>
                            &emsp;&emsp;
                        <label for="editor-area-theme-select">
                            编辑区域：
                        </label>
                        <select id="editor-area-theme-select" name="editortheme">
                            <option selected="selected" value="">选择编辑器主题</option>
                        </select>
                            &emsp;&emsp;
                        <label for="preview-area-theme-select">
                            预览区域：
                        </label>
                        <select id="preview-area-theme-select" name="previewtheme">
                            <option selected="selected" value="">选择实时预览主题</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="Themes">保存主题</button>
                        <div class="layui-form-mid layui-word-aux"><i class="layui-icon" style="font-size:30px" id="theme-tip">&#xe60b;</i></div>
                    </div>
                    <input type="hidden" name="type" value="theme">
                </form>
                
                <div style="width:100%">
                    <div id="test-editormd"></div>
                </div>
            </div>
            <div class="layui-tab-item">
                <fieldset class="layui-elem-field">
                    <legend>升级更新说明</legend>
                    <div class="layui-field-box">
                        <p>在更新之后出现的一些问题，可以通过以下操作解决：</p>
                        <p>● 在插件管理页面“停用”后再“启用”本插件。</p>
                        <p>如果问题仍未解决：</p>
                        <p>● 在插件管理页面删除本插件，再从应用中心重新安装。</p>
                        <p>如果问题仍未解决：</p>
                        <p>● 请及时通过下方的联系方式将问题反馈给开发者。</p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>第三方 MIP 主题兼容说明</legend>
                    <div class="layui-field-box">
                        <p>插件默认支持 ZBLOG 官方 MIP 插件，如果使用了依赖 ZBLOG 官方 MIP 插件标准的主题，则可以关闭兼容，插件会自动适配。</p>
                        <p>如果使用不依赖 ZBLOG 官方 MIP 插件的第三方主题，请启用兼容。</p>
                        <p>如何辨别主题是否使用了 ZBLOG 官方 MIP 插件依赖？如果主题要求同时安装启用名为“MIP支持插件”（作者：zsx）的插件，则表示该主题依赖 ZBLOG 官方 MIP 插件。</p>
                        <hr class="layui-bg-red">
                        <p>注意：由于 MIP 的限制，在对应的 MIP 主题下，所有的编辑器扩展（包括ToC目录、GFM任务列表等）都不被支持，请知悉。</p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>扩展包说明</legend>
                    <div class="layui-field-box">
                        <?php if ($zbp->Config('Editormd')->getpack == 'false' && file_exists(__DIR__ . '/images/github-emojis') && file_exists(__DIR__ . '/lib/katex')): ?>
                        <strong>当前状态：您已经成功获取扩展包！</strong>
                        <?php else: ?>
                        <strong>当前状态：您还没有获取扩展包。</strong>
                        <?php endif;?>
                        <p>由于 emoji 和 katex 扩展功能所需要的文件体积过大，出于减小插件体积的考虑，本插件使用扩展包机制，在插件安装完成并进入设置页面时自动获取额外的扩展包，由此缩短插件下载时间，避免个别服务器下载出错等。</p>
                        <p>扩展包只获取一次，成功之后都不需要再次获取。</p>
                        <p>目前下载的扩展包有两个：github-emoji 表情包和 katex 科学公式包。</p>
                        <p>如果扩展包获取失败，或者不想使用相关的功能，可以关闭弹窗暂停获取。日后也可以重新获取。</p>
                        <p>没有扩展包，只会影响 emoji 和 Katex 科学公式的功能，其他功能可以正常使用，只需在设置中将这两个功能关闭即可。</p>
                        <p>远程扩展包存放在七牛云上，你可以通过最下方的链接注册七牛云来支持开发者。</p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>HTML 解析说明</legend>
                    <div class="layui-field-box">
                        <p>HTML 解析功能可以解析 Markdown 原文中包含的 HTML 标签。</p>
                        <p>如果为“开启,全部解析”状态，则默认解析所有标签。</p>
                        <p>也可以选择“开启,全部解析”来过滤指定标签及属性的解析，提高安全性。</p>
                        <p>请参考：<a href="https://pandao.github.io/editor.md/examples/html-tags-decode.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">https://pandao.github.io/editor.md/examples/html-tags-decode.html</a></p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>编辑器扩展功能说明</legend>
                    <div class="layui-field-box">
                        <p>编辑器扩展可以实现 ToC/ToCM 目录、Github Flavored Markdown(GFM) 任务列表、Tex 科学公式语言、流程图、时序图/序列图等功能。</p>
                        <p>开启编辑器扩展后，前台文章页面还将额外输出原始的 Markdown 内容，然后通过 Editor.md 的“HTML预览”这一 Javascript 组件将内容动态转化为 HTML 文档。该操作将一定程度地导前致台页面体积增大，拖慢网页的展现速度。</p>
                        <p>关闭编辑器扩展功能后，原有的相关扩展内容也将无法正常展示，开启后恢复正常。</p>
                        <hr class="layui-bg-red">
                        <h3>ToC/ToCM 目录</h3>
                        <p>使用示例：</p>
                        <p>在需要显示目录的地方插入“ [TOC] ”</p>
                        <p>在需要显示下拉菜单目录的地方插入“ [TOCM] ”</p>
                        <p>插件将自动检测文档中的标题并在顶部显示目录。</p>
                        <p>请参考：<a href="https://pandao.github.io/editor.md/examples/toc.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">https://pandao.github.io/editor.md/examples/toc.html</a></p>
                        <hr class="layui-bg-blue">
                        <h3>GFM 任务列表</h3>
                        <p>使用示例：</p>
                        <p>- [] 待办列表1</p>
                        <p>- [x] 待办列表2</p>
                        <p>- [x] 待办列表3</p>
                        <p>  &emsp;  - [x] 待办子列表3-1</p>
                        <p>  &emsp;  - [x] 待办子列表3-2</p>
                        <p>其中[x]表示勾选。</p>
                        <p>请参考：<a href="https://pandao.github.io/editor.md/examples/task-lists.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">https://pandao.github.io/editor.md/examples/task-lists.html</a></p>
                        <hr class="layui-bg-green">
                        <h3>Tex 科学公式语言 (TeX/LaTeX)</h3>
                        <p>支持通过 TeX/LaTeX 来展示各类科学、数学公式。</p>
                        <p>插件使用的是本地文件，而非 CloudFlare 的 CDN。</p>
                        <p>请参考：<a href="https://pandao.github.io/editor.md/examples/katex.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">https://pandao.github.io/editor.md/examples/katex.html</a></p>
                        <hr class="layui-bg-orange">
                        <h3>流程图</h3>
                        <p>通过矢量绘图实现简单的流程图展示。</p>
                        <p>请参考：<a href="https://pandao.github.io/editor.md/examples/flowchart.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">https://pandao.github.io/editor.md/examples/flowchart.html</a></p>
                        <hr class="layui-bg-gray">
                        <h3>时序图/序列图</h3>
                        <p>动态展现时序图/序列图。</p>
                        <p>请参考：<a href="https://pandao.github.io/editor.md/examples/sequence-diagram.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">https://pandao.github.io/editor.md/examples/sequence-diagram.html</a></p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>Emoji 表情说明</legend>
                    <div class="layui-field-box">
                        <p>如果想在文章中使用 Font Awesome 图标，则需要同时开启“编辑器扩展”功能。如果只使用 Github Emoji 和 Twemoji ，则只需要单独开启 emoji 就能正常使用，无需再开启“编辑器扩展”（当然，开启也行）。</p>
                        <p>Github Emoji 表情图标和 Font Awesome 字体图标均使用本地文件，Twemoji 使用 maxcdn 的文件。</p>
                        <p>如果删除本插件，原有的 Github Emoji 和 Font Awesome 将无法展示，重新安装插件可恢复正常。</p>
                        <p>Emoji 在摘要当中无法正常显示，所以请手动编辑不含 Emoji 的摘要。</p>
                        <p>Github Emoji 需要获取扩展包之后方可正常使用。</p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>粘贴图片上传说明</legend>
                    <div class="layui-field-box">
                        <p>本插件支持剪贴板图片粘贴上传，即通过截图工具（如QQ截图、Snipaste）截图并复制到剪贴板、或者通过键盘上的 PrintScrn 按键截图的图片，不支持从文件管理器复制粘贴（因为复制的只是路径，不是文件）。</p>
                        <p>复制好图片后，在编辑框内直接粘贴即可自动上传显示。</p>
                        <p>支持最新的Chrome（包括360极速浏览器等等）、Firefox、Opera、Edge浏览器，不支持IE。</p>
                        <p>感谢  <a href="https://www.zsxsoft.com/" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">@zsx</a> 提供的代码。</p>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>关于</legend>
                    <div class="layui-field-box">
                        <p>插件作者：心扬</p>
                        <p>联系方式：chrisyoungpark@qq.com</p>
                        <p>BUG或建议反馈：发邮件，或者 <strong><a href="https://bbs.zblogcn.com/thread-93729-1-1.html" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">到论坛回帖/私信开发者</a></strong> 或者 <strong><a href="<?php echo $zbp->host;?>zb_users/plugin/AppCentre/main.php?id=1408" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">在应用中心评论</a></strong></p>
                        <p></p>
                        <p>更多详情：<b><a href="https://pandao.github.io/editor.md/" target="_blank" style="color:rgb(0,0,238);text-decoration:underline;">Editor.md 项目主页</a></b></p>
                        <p class="support"><a class="layui-btn layui-btn-normal" href="<?php echo $zbp->host;?>zb_users/plugin/AppCentre/main.php?auth=2ffbff0a-1207-4362-89fb-d9a780125e0a" style="color: #FFFFFF">开发者的其他作品</a> <a target="_blank" class="layui-btn layui-btn-warm" href="https://portal.qiniu.com/signup?code=3lcny2lm7nwb6" style="color: #FFFFFF">免费注册七牛云支持开发者</a> <button id="donation" class="layui-btn layui-btn-danger">请我喝咖啡（捐赠）</button><!--<button id="emdpack" class="layui-btn layui-btn-danger">打包</button>--></p>
                    </div>
                </fieldset>

                <div class="copyright">
                    <p><img src="<?php echo $zbp->host; ?>zb_users/plugin/Editormd/logo.png" alt="logo"></p>
                    <p>Editormd v<?php $app=new App;$app->LoadInfoByXml('plugin', 'Editormd');echo $app->version; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="donation_layer">
    <img src="http://oznuv0a19.bkt.clouddn.com/wxzanshang.jpg" alt="" width="460px" height="460px">
</div>

<style>
#divMain a, #divMain2 a {
    color: #666;
}
#divMain a:hover, #divMain2 a:hover {
    color: #666;
}
.editorbtn {
    background: #3a6ea5;
    width: 100px;
    line-height: 200%;
    color: #fff;
    text-align: center;
    margin: 0 0 15px 10px;
    display: inline-block;
}
.editorbtn:hover {
    cursor: pointer;
}
div.editorcr {
    text-align: right;
}
p.support {
    margin-top: 15px;
}
div.copyright {
    text-align: center;
    margin-bottom: 20px;
}
form.theme {
    margin: 10px;
}
form.theme select {
    height: 30px;
}
#theme-select {
    display: inline;
    margin-right: 20px;
}
.layui-field-box {
    line-height: 150%;
}
/*layer fiixed*/

/*From Fixed*/
.layui-form-label {
    width: 100px;
}
/*以下为了适应ZBP后台自带的admin2.css*/
* {
    box-sizing: content-box
}
input[readonly], input[readonly]:hover {
    background: #FFFFFF;
    border: solid 1px #D2D2D2;
    cursor: pointer;
    color: #000000;
}
hr {
    visibility: visible;
}
/* donation */
#donation_layer {
    display: none;
}
#donation_layer li {
    padding: 15px;
    margin: 10px;
    border: 1px solid #efefef;
    border-radius: 8px;
    word-wrap: break-word;
}
</style>

<script src="<?php echo $zbp->host; ?>zb_users/plugin/Editormd/editormd.min.js"></script>
<script src="<?php echo $zbp->host; ?>zb_users/plugin/Editormd/css/layui/layui.js" charset="utf-8"></script>

<script type="text/javascript">
var testEditor;

//模块化加载layui
layui.use(['layer', 'form', 'element'], function(){
    var layer = layui.layer   //获得layer模块
    ,form     = layui.form    //获得form模块
    ,element  = layui.element; //获得element模块

    <?php if ($getpack): ?>
    var getting = layer.open({
        type: 3
        ,icon: 1
        ,shadeClose: false
        ,scrollbar: false
        ,resize: false
    });
    $('#layui-layer'+getting).append('<div style="color:#FFFFFF;position:relative;top:10px;right:60px;">正在获取扩展包，请耐心等待……</div>');
    $.get('setting.php', function(result){
        result = JSON.parse(result);
        layer.close(getting);
        if (result[0]) {
            layer.open({
                title: '操作成功'
                ,content: result[1]
                ,shadeClose: true
                ,scrollbar: false
                ,resize: false
                ,end: function(){
                    location.reload();
                }
            });
        } else {
            layer.open({
                title: '操作失败'
                ,content: result[1] + '点击确认重试，点击右上角关闭按钮停止获取。'
                ,shadeClose: true
                ,scrollbar: false
                ,resize: false
                ,yes: function(index, layero){
                    location.reload();
                }
                ,cancel: function(index, layero) {
                    if(confirm('暂时不再获取扩展包，但是emoji和katex将不可用，详情请看帮助说明。')){
                        $.get('setting.php?action=delay');
                        layer.close(index);
                    }
                    return false; 
                }
            });
        }
        
    });
    <?php endif; ?>

    //-----------Tabs Operations Start----------
    //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
    layid = location.hash.replace(/^#tabs=/, '');
    element.tabChange('tabs', layid);

    //监听Tab切换，以改变地址hash值
    element.on('tab(tabs)', function(){
        location.hash = 'tabs='+ this.getAttribute('lay-id');
        if (testEditor==undefined) {
            loadEditormd();
        }
    });

    if (layid == 'preview') {
        loadEditormd();
    }
    //-----------Tabs Operations End----------

    //-----------Form Operations Start--------
    //监听功能配置提交
    form.on('submit(Setting)', function(data){
        $.post('setting.php', data.field, function (result) {
            result = JSON.parse(result);
            layer.open({
                title: '操作提示'
                ,content: result[1]
                ,shadeClose: true
                ,yes: function(index, layero){
                    location.reload();
                }
            });
        });
        
        return false;
    });
    //监听主题保存
    form.on('submit(Themes)', function(data){
        console.log($(data.form).serializeArray());

        $.post('setting.php', $(data.form).serializeArray(), function (result) {
            result = JSON.parse(result);
            layer.open({
                title: '操作提示'
                ,content: result[1]
                ,shadeClose: true
                ,yes: function(index, layero){
                    location.reload();
                }
            });
        });
        
        return false;
    });
    //-----------Form Operations End---------

    //-----------Tips Operations Start-------
    //动态主题
    $('i#dynamic-tip').mouseover(function(){
        var tip = layer.tips('为编辑器添加动态主题菜单栏', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //emoji
    $('i#emoji-tip').mouseover(function(){
        var tip = layer.tips('emoji 表情图标功能，需要扩展包，详情查看帮助说明', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //自动长高
    $('i#ah-tip').mouseover(function(){
        var tip = layer.tips('编辑区域的高度随内容的增长而增长', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //预览
    $('i#pre-tip').mouseover(function(){
        var tip = layer.tips('右侧预览区域的默认开关，同时也可以在编辑器的工具栏中设定', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //双向滚动
    $('i#scroll-tip').mouseover(function(){
        var tip = layer.tips('双向：预览区域(右侧)与编辑区域(左侧)同时滚动<br>单向：只有右侧跟随左侧滚动<br>禁用：两侧都不跟随滚动', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //前台代码主题
    $('i#ct-tip').mouseover(function(){
        var tip = layer.tips('为前台代码定义样式，在关闭编辑器扩展时生效', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //第三方MIP兼容
    $('i#mip-tip').mouseover(function(){
        var tip = layer.tips('开启后可以兼容第三方MIP主题<br>如果使用了依赖官方MIP插件的MIP主题，请关闭兼容<br>详情查看帮助说明', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //HTML解析
    $('i#hd-tip').mouseover(function(){
        var tip = layer.tips('开启后可以解析编辑器中的 HTML 标签<br>详情查看帮助说明', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //HTML解析过滤标签
    $('i#hf-tip').mouseover(function(){
        var tip = layer.tips('HTML 解析过滤的标签，可以为空', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //重置HTML解析过滤标签
    $('i#hf-reset').mouseover(function(){
        var tip = layer.tips('点击恢复默认', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).click(function(){
            $("input[name='htmlfilter']").val("style,script,iframe|on*");
        });
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //扩展功能
    $('i#ext-tip').mouseover(function(){
        var tip = layer.tips('扩展功能，包括TOC目录、任务列表、科学公式、流程图、时序图等功能<br>详情查看帮助说明', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //科学公式
    $('i#tex-tip').mouseover(function(){
        var tip = layer.tips('Katex科学公式，需要扩展包，详情查看帮助说明', this, {anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //主题保存
    $('i#theme-tip').mouseover(function(){
        var tip = layer.tips('保存主题后，即使关闭了动态主题，设置的主题仍然生效', this, {tips:1,anim:5,time:0,isOutAnim:false,maxWidth:500});
        $(this).mouseleave(function(){
            layer.close(tip);
        });
    });
    //-----------Tips Operations End---------

    //----------Donation Start------------
    $('button#donation').click(function(){
        layer.open({
            type: 1,
            title: '以下为微信赞赏码，请打开微信扫一扫',
            maxWidth: 460,
            content: $('#donation_layer')
        });
    });
    //----------Donation End--------------

    //---------------Pack Start-------------
    /*$('button#emdpack').click(function(){
        $.get('setting.php?action=pack', function(result) {
            result = JSON.parse(result);
            layer.open({
                title: 'Result'
                ,content: result[1]
                ,shadeClose: true
                ,yes: function(index, layero){
                    layer.close(index);
                }
            });
        });
    });*/
    //----------------Pack End--------------
});

$(function(){
    localStorage['theme']        = '<?php echo $toolbartheme; ?>';
    localStorage['editorTheme']  = '<?php echo $editortheme; ?>';
    localStorage['previewTheme'] = '<?php echo $previewtheme; ?>';
    
    themeSelect("editormd-theme-select", editormd.themes, "theme", function($this, theme){
        testEditor.setTheme(theme);
    });

    themeSelect("editor-area-theme-select", editormd.editorThemes, "editorTheme", function($this, theme) {
        testEditor.setCodeMirrorTheme(theme);
        // or testEditor.setEditorTheme(theme);
    });

    themeSelect("preview-area-theme-select", editormd.previewThemes, "previewTheme", function($this, theme) {
        testEditor.setPreviewTheme(theme);
    });
});

//动态加载编辑器
var loadEditormd = function ()
{
    editormd.emoji = {
        path : "<?php echo $zbp->host; ?>zb_users/plugin/Editormd/images/github-emojis/",
        ext  : ".png"
    };
    $.get('preview.md', function(md){
        testEditor = editormd("test-editormd", {
            width : '100%',
            height: 640,
            tocm  : true,
            markdown : md,
            codeFold : true,
            emoji       : true,
            imageUpload : false,
            theme        : (localStorage.theme) ? localStorage.theme : "<?php echo $toolbartheme;?>",
            previewTheme : (localStorage.previewTheme) ? localStorage.previewTheme : "<?php echo $previewtheme;?>",
            editorTheme  : (localStorage.editorTheme) ? localStorage.editorTheme : "<?php echo $editortheme;?>",
            path         : '<?php echo $zbp->host; ?>zb_users/plugin/Editormd/lib/'
        });
    });
}

//编辑器主题切换
var themeSelect = function (id, themes, lsKey, callback)
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
</script>

<?php
require __DIR__ . '/../../../zb_system/admin/admin_footer.php';
RunTime();
?>
