module.exports = function(grunt) {
    
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        //Js min task
        uglify: {
            acp: {//acp
                options: {
                    banner:'/*Create date <%= grunt.template.today("yyyy-mm-dd") %> '+
                            'modules:jquery1.8.3 jquery.validate jquery.jpops.min-v3 main tabs */'
                },
                files: {
                    'Js/acp/acpcomm-min.js': [
                        'Js/jquery/jquery.validate.js',
                        'Js/jquery/jquery.cookie.min.js',
                        'Js/acp/main.js',
                        'Js/ModulesJs/tabs.js'
                    ]
                }
            },
            decorate: {//装修模式
                options: {
                    banner:'/*Create date <%= grunt.template.today("yyyy-mm-dd") %> */'
                },
                files: {
                    //所有页面基础框架
                    'Js/decorate/ui_frame-min.js': [
                        'Js/decorate/ui_frame.js'
                    ],
                    //装修模式
                    'Js/decorate/decorate-min.js': [//装修模式
                        'Js/jquery/jquery-ui-1.9.2.draggable.min.js',
                        'Js/jquery/uploadify/swfobject.js',
                        'Js/jquery/uploadify/jquery.uploadify.v2.1.4.min.js',
                        '../KD/kindeditor.js',
                        '../KD/lang/zh_CN.js',
                        'Plugins/jPops/jquery.jpops.min-v3.js',
                        'Plugins/farbtastic/farbtastic.min.js',
                        'Js/decorate/ui_decorate_addlayout.js',
                        'Js/decorate/ui_decorate_Tem.js',
                        'Js/decorate/ui_decorate_editbar.js',
                        'Js/decorate/ui_decorate_popup.js',
                        'Js/decorate/ui_decorate_core.js',
                        'Js/decorate/ui_decorate_uploadimg.js'
                    ],
                }
            }
        },
        //Css min task
        cssmin: {
            acp: {//acp
                options: {
                    banner:'/*Create date <%= grunt.template.today("yyyy-mm-dd") %> '+
                            'modules:reset layout-fx icons-fx forms-fx gicons buttons-fx tables-fx pagination-fx tabs-fx chartpanel-fx jquery.jpops-v3*/'
                },
                files: {
                    'Css/acp/acpcomm-min.css': [
                        'Css/ModulesCss/reset.css',
                        'Css/ModulesCss/layout-fx.css',
                        'Css/ModulesCss/icons-fx.css',
                        'Css/ModulesCss/forms-fx.css',
                        'Css/ModulesCss/gicons.css',
                        'Css/ModulesCss/buttons-fx.css',
                        'Css/ModulesCss/tables-fx.css',
                        'Css/ModulesCss/pagination-fx.css',
                        'Css/ModulesCss/tabs-fx.css',
        				'Css/ModulesCss/chartpanel-fx.css'
                    ]
                }
            },
            decorate: {//装修模式
                options: {
                    banner:'/*Create date <%= grunt.template.today("yyyy-mm-dd") %> */'
                },
                files: {
                    //所有页面基础样式
                    'Css/decorate/base-min.css': [//正常模式
                        'Css/ModulesCss/reset.css',
                        'Css/decorate/ui_frame.css'
                    ],
                    //装修模式样式
                    'Css/decorate/uidecorate-min.css': [//装修模式
                        'Css/decorate/ui_decorate.css',
                        'Css/ModulesCss/gicons.css',
                        'Css/ModulesCss/forms-fx.css',
                        'Css/ModulesCss/button.css',
                        'Plugins/farbtastic/farbtastic.min.css',
                        'Plugins/jPops/jquery.jpops-v3.css',
                        'Js/jquery/uploadify/uploadify.css',
                        '../KD/themes/default/default.css',
                    ]
                }
            }
        }
    });

    // load npm tasks
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');

    // resgister tasks
    grunt.registerTask('default', ['uglify', 'cssmin']);

};

// ACP
// <link rel="stylesheet" href="__CSS__/ModulesCss/reset.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/layout-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/icons-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/forms-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/gicons.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/buttons-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/tables-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/pagination-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/tabs-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/chartpanel-fx.css">
// <link rel="stylesheet" href="__PLG__/jPops/jquery.jpops.min-v3.css">

// <script src="__JS__/jquery/jquery-1.8.3.min.js"></script>
// <script src="__JS__/jquery/jquery.validate-min.js"></script>
// <script src="__JS__/jquery/jquery.cookie.min.js"></script>
// <script src="__PLG__/jPops/jquery.jpops.min-v3.js"></script>
// <script src="__ACPJS__/main.js"></script>
// <script src="__JS__/ModulesJs/tabs.js"></script>


//===装修模式
// 页面基础框架 开始 上线合并文件为 ./decorate/base-min.css
// <link rel="stylesheet" href="__CSS__/ModulesCss/reset.css">
// <link rel="stylesheet" href="__CSS__/decorate/ui_frame.css">

// 装修模式UI样式 开始 上线合并文件为 ./decorate/uidecorate-min.css
// <link rel="stylesheet" href="__CSS__/decorate/ui_decorate.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/gicons.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/forms-fx.css">
// <link rel="stylesheet" href="__CSS__/ModulesCss/button.css">
// <link rel="stylesheet" href="__PUBLIC__/Plugins/farbtastic/farbtastic.min.css">
// <link rel="stylesheet" href="__PUBLIC__/Plugins/jPops/jquery.jpops.min-v3.css">
// <link rel="stylesheet" href="__JS__/jquery/uploadify/uploadify.css">
// <link rel="stylesheet" href="__KD__/themes/default/default.css" />

// 框架必须的 上线合并ui_frame-min.js
// <script src="__JS__/decorate/ui_frame.js"></script>

// 装修模式UI js 开始 上线合并为 decorate-min.js
// <script src="__JS__/jquery/jquery-ui-1.9.2.draggable.min.js"></script>
// <script src="__JS__/jquery/uploadify/swfobject.js"></script>
// <script src="__JS__/jquery/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
// <script charset="utf-8" src="__KD__/kindeditor.js" > </script>
// <script charset="utf-8" src="__KD__/lang/zh_CN.js" > </script>
// <script src="/Public/Plugins/jPops/jquery.jpops.min-v3.js"></script>
// <script src="__PUBLIC__/Plugins/farbtastic/farbtastic.min.js"></script>
// <script src="__JS__/decorate/ui_decorate_addlayout.js"></script>
// <script src="__JS__/decorate/ui_decorate_Tem.js"></script>
// <script src="__JS__/decorate/ui_decorate_editbar.js"></script>
// <script src="__JS__/decorate/ui_decorate_popup.js"></script>
// <script src="__JS__/decorate/ui_decorate_core.js"></script>
// <script src="__JS__/decorate/ui_decorate_uploadimg.js"></script>