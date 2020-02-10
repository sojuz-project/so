import './acf-form.view.scss';
import './acf-form.editor.scss';

const {
    registerBlockType,
} = wp.blocks;

const { 
    InspectorControls,
    RichText
} = wp.editor;

registerBlockType('sojuz/acf-form', {
    title: 'acf-form',
    icon: 'clipboard',
    category: 'layout',

    supports: {
        align: true,
    },

    attributes: {
        align: true,
    },

    edit(props) {
        const { className, setAttributes } = props;
        // const {  } = props.attributes;

        return [
            <InspectorControls>
                <div style={{padding: '1em 0'}}>
                    Options
                </div>
            </InspectorControls>,
            <div className={className}>
                acf-form
            </div>,
        ];
    },

    save(props) {
        // const className = getBlockDefaultClassName('sojuz/acf-form'); // For use with say, BEM
        // const {  } = props.attributes;

        return (
            <div>
                acf-form
            </div>
        );
    },
});
