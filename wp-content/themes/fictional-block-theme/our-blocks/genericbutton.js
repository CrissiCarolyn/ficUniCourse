import ourColours from "../inc/ourColours"
import { link } from "@wordpress/icons"
import { ToolbarGroup, ToolbarButton, Popover, Button, PanelBody, PanelRow, ColorPalette } from "@wordpress/components"
import { RichText, InspectorControls, BlockControls, __experimentalLinkControl as LinkControl, getColorObjectByColorValue } from "@wordpress/block-editor"
import { registerBlockType } from "@wordpress/blocks"
import { useState } from "@wordpress/element"


registerBlockType("ourblocktheme/genericbutton", {
    title: "Generic Button",
    attributes: {
        text: {type: "string"},
        size: {type: "string", default: "large"},
        linkObject: {type: "object", default: {url: ""} },
        colourName: {type: "string", default: "blue" }
    },
    edit: EditComponent,
    save: SaveComponent
})

function EditComponent(props) {
    const [isLinkPickerVisible, setIsLinkPickerVisible] = useState(false)




    function handleTextChange(x) {
        props.setAttributes({text: x})

    }


    function buttonHandler() {
        setIsLinkPickerVisible(prev => !prev)

    }

    function handleLinkChange(newLink) {
        props.setAttributes({linkObject: newLink})

    }



    const currentColourValue = ourColours.filter(color => {
        return color.name == props.attributes.colourName
    })[0].color

    function handleColourChange(colourCode) {
        //find and output colour name from hex colour palette 
        const { name } = getColorObjectByColorValue(ourColours, colourCode)
        props.setAttributes({ colourName: name })

    }

    return (
        <>

        <BlockControls> 
            <ToolbarGroup>
                <ToolbarButton onClick={buttonHandler} icon={link} />
            </ToolbarGroup>
            <ToolbarGroup>
                <ToolbarButton isPressed={props.attributes.size === "large"} onClick={() => props.setAttributes({size: "large"})}>Large</ToolbarButton>
                <ToolbarButton isPressed={props.attributes.size === "medium"} onClick={() => props.setAttributes({size: "medium"})}>Medium</ToolbarButton>
                <ToolbarButton isPressed={props.attributes.size === "small"} onClick={() => props.setAttributes({size: "small"})}>Small</ToolbarButton>
            </ToolbarGroup>
        </BlockControls>
        <InspectorControls>
            <PanelBody title="Colour" initialOpen={true}>
                <PanelRow>
                    <ColorPalette disableCustomColors={true} clearable={false} colors={ourColours} value={currentColourValue} onChange={handleColourChange} />
                </PanelRow>
            </PanelBody>
           
        </InspectorControls>


            <RichText 
            allowedFormats={[]} 
            tagName="a" 
            className={`btn btn--${props.attributes.size} btn--${props.attributes.colourName}`} 
            value={props.attributes.text} 
            onChange={handleTextChange} 
            />
        {isLinkPickerVisible && (
            (<Popover onFocusOutside={() => setIsLinkPickerVisible(false)} >
                <LinkControl settings={[]} value={props.attributes.linkObject} onChange={handleLinkChange} />
                <Button variant="primary" onClick={() => setIsLinkPickerVisible(false)} style={{display: "block", width: "100%"}} >Confirm Link</Button>



            </Popover>)
        )}
        </>
    )

}

function SaveComponent(props) {
    
    return ( 
        <a href={props.attributes.linkObject.url} className={`btn btn--${props.attributes.size} btn--${props.attributes.colourName}`}>
            {props.attributes.text} 
        </a>
    ) 

}