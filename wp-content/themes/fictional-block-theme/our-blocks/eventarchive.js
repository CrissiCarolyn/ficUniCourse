
wp.blocks.registerBlockType("ourblocktheme/eventarchive", {
    title: "FU Events Archive",


    edit: function() {
        return wp.element.createElement("div", {className: "our-placeholder-block"}, "Events Archive Placeholder")
    },
    save: function() {
        return null
    }
})
