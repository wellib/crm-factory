$(function () {
    $("#structure-table").fancytree({
        extensions: ["dnd", "glyph", "table"],
        checkbox: false,
        dnd: {
            preventVoidMoves: true,
            preventRecursiveMoves: true,
            focusOnClick: true,
            dragStart: function (node, data) {
                return true;
            },
            dragEnter: function (node, data) {
                return true;
            },
            dragDrop: function (node, data) {
                data.otherNode.moveTo(node, data.hitMode);

                $.ajax({
                    url: 'update',
                    type: 'POST',
                    data: {nodes: node.tree.toDict()},
                    dataType: 'json'
                });
            }
        },
        glyph: {
            map: {
                doc: "fa fa-user",
                docOpen: "fa fa-user",
                checkbox: "glyphicon glyphicon-unchecked",
                checkboxSelected: "glyphicon glyphicon-check",
                checkboxUnknown: "glyphicon glyphicon-share",
                dragHelper: "glyphicon glyphicon-play",
                dropMarker: "glyphicon glyphicon-arrow-right",
                error: "glyphicon glyphicon-warning-sign",
                expanderClosed: "glyphicon glyphicon-menu-right",
                expanderLazy: "glyphicon glyphicon-menu-right",  // glyphicon-plus-sign
                expanderOpen: "glyphicon glyphicon-menu-down",  // glyphicon-collapse-down
                folder: "glyphicon glyphicon-folder-close",
                folderOpen: "glyphicon glyphicon-folder-open",
                loading: "glyphicon glyphicon-refresh glyphicon-spin"
            }
        },
        source: treeUsers
    });
});