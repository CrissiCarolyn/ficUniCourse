
wp.blocks.registerBlockType("ourblocktheme/singlecampus", {
    title: "FU Single Campus",


    edit: function() {
        return wp.element.createElement("div", {className: "our-placeholder-block"}, "Single Campus Placeholder")
    },
    save: function() {
        return null
    }
})
