
wp.blocks.registerBlockType("ourblocktheme/programarchive", {
    title: "FU Custom Program Archive",


    edit: function() {
        return wp.element.createElement("div", {className: "our-placeholder-block"}, "Program Archive Placeholder")
    },
    save: function() {
        return null
    }
})
