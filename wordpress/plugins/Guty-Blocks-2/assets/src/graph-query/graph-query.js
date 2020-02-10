import './graph-query.view.scss';
import './graph-query.editor.scss';

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { InnerBlocks, BlockControls, InspectorControls, PlainText, AlignmentToolbar, ColorPalette } = wp.editor;
const { DropdownMenu, Toolbar, PanelBody, ToggleControl } = wp.components;

registerBlockType('sojuz/graph-query', {
    title: 'GraphQL blocks query',
    icon: 'schedule',
    category: 'layout',
    description: 'Native SOJUZ project block to render custom components',
    supports: {
        align: ['full', 'wide'],
    },

    supports: {
        align: ['full', 'wide'],
    },

    attributes: {
        align: {
            type: 'string',
            default: '',
        },
        backgroundColor: {
            type: 'string',
            default: 'initial',
        },
        componentParentName: {
            type: 'string',
            default: 'coregroup',
        },
        componentItemName: {
            type: 'string',
            default: 'coregroup',
        },
        query: {
            type: 'string',
            default: '',
        },
        queryAlias: {
            type: 'string',
            default: '',
        },
        queryVariables: {
            type: 'bollean',
            default: false,
        },
        'component-attrs': {
            type: 'string',
            default: '',
        },
    },

    edit: (props) => {
        const { attributes: {
            align,
            backgroundColor,
            componentParentName,
            componentItemName,
            'component-attrs': componentAttrs,
            query,
            queryAlias,
            queryVariables,
        }, className, setAttributes } = props;

        return (
            <div className={className} style={{ backgroundColor: backgroundColor }}>
                <InspectorControls>

                    {<PanelBody
                        title={__('Wrapper properties')}
                        initialOpen={true}
                    >
                        <label>Wrapper component name</label>
                        <PlainText
                            className="plain-text"
                            value={componentParentName}
                            onChange={(componentParentName) => setAttributes({ componentParentName })}
                        />
                        <label>Component attrs (JSON)</label>
                        <PlainText
                            className="plain-text-code"
                            value={componentAttrs}
                            onChange={(componentAttrs) => setAttributes({ 'component-attrs': componentAttrs })}
                        />
                    </PanelBody>}

                    {<PanelBody
                        title={__('Query properties')}
                        initialOpen={true}
                    >

                        <label>Query body (GQL)</label>
                        <PlainText
                            className="plain-text-code"
                            value={query}
                            onChange={(query) => setAttributes({ query })}
                        />
                        <label>Query alias </label>
                        <PlainText
                            className="plain-text"
                            value={queryAlias}
                            onChange={(queryAlias) => setAttributes({ queryAlias })}
                        />
                        <label>Item component name</label>
                        <PlainText
                            className="plain-text"
                            value={componentItemName}
                            onChange={(componentItemName) => setAttributes({ componentItemName })}
                        />
                        <hr></hr>
                        <ToggleControl
                            label="Query variables"
                            help={queryVariables ? 'Get variables from routing' : 'Disable query variables'}
                            checked={queryVariables}
                            onChange={(queryVariables) => setAttributes({ queryVariables })}
                        />
                    </PanelBody>}

                    <PanelBody
                        title={__('Appearance')}
                        initialOpen={false}
                    >
                        { /* Block background  */}
                        <label>Background color</label>
                        <ColorPalette value={backgroundColor} onChange={(backgroundColor) => setAttributes({ backgroundColor })} />
                    </PanelBody>

                </InspectorControls>

                <InnerBlocks />
               
            </div>
        );
    },
    save: (props) => {
        // console.log(props);
        return (
            <div>
                <InnerBlocks.Content />
            </div>
        );
    },
});
