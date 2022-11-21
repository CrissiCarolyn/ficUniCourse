
wp.blocks.registerBlockType("ourblocktheme/footer", {
    title: "FU Custom Footer",


    edit: function() {
        return wp.element.createElement("div", {className: "our-placeholder-block"}, "Footer Placeholder")
    },
    save: function() {
        return null
    }
})
