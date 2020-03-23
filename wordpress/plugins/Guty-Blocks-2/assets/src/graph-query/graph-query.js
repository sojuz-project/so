/* eslint-disable react/display-name */
/* eslint-disable react/jsx-key */
/* eslint-disable react/react-in-jsx-scope */

import './graph-query.view.scss';
import './graph-query.editor.scss';

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { InnerBlocks, BlockControls, InspectorControls, PlainText, AlignmentToolbar, ColorPalette } = wp.editor;
const { DropdownMenu, Toolbar, PanelBody, ToggleControl, ButtonGroup, Button } = wp.components;

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
      default: 'has-default',
    },
    backgroundOpacity: {
      type: 'string',
      default: '1',
    },
    itemsLimit: {
      type: 'string',
      default: 12,
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
    resTarget: {
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
    const {
      attributes: {
        align,
        backgroundColor,
        backgroundOpacity,
        componentParentName,
        componentItemName,
        'component-attrs': componentAttrs,
        query,
        queryAlias,
        queryVariables,
        resTarget,
        itemsLimit,
      },
      className,
      setAttributes,
    } = props;

    const colors = [
      { name: 'Primary', slug: 'has-primary', color: '#0073a8' },
      { name: 'Secondary', slug: 'has-secondary', color: '#005075' },
      { name: 'Dark gray', slug: 'has-dark-gray', color: '#111111' },
      { name: 'Light gray', slug: 'has-light-gray', color: '#767676' },
      { name: 'White', slug: 'has-white', color: '#ffffff' },
      { name: 'Default', slug: 'has-default', color: '#ffffff' },
    ];

    return (
      <div
        className={className}
        style={{
          backgroundColor: colors.find((el) => {
            return el.slug == backgroundColor;
          }).color,
        }}>
        <InspectorControls>
          <label>Component attrs (JSON)</label>
          <PlainText
            className="plain-text-code"
            value={componentAttrs}
            onChange={(componentAttrs) => setAttributes({ 'component-attrs': componentAttrs })}
          />
          <PanelBody title={__('Query properties')} initialOpen={true}>
            <label>Query body (GQL)</label>
            <PlainText className="plain-text-code" value={query} onChange={(query) => setAttributes({ query })} />
            <label>Query alias </label>
            <PlainText
              className="plain-text"
              value={queryAlias}
              onChange={(queryAlias) => setAttributes({ queryAlias })}
            />
            <label>Responce target</label>
            <PlainText
              className="plain-text"
              value={resTarget}
              onChange={(resTarget) => setAttributes({ resTarget })}
            />
            <label>Wrapper component name</label>
            <PlainText
              className="plain-text"
              value={componentParentName}
              onChange={(componentParentName) => setAttributes({ componentParentName })}
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
            <label>Items limit</label>
            <PlainText
              className="plain-text"
              value={itemsLimit}
              onChange={(itemsLimit) => setAttributes({ itemsLimit })}
            />
          </PanelBody>

          <PanelBody title={__('Appearance')} initialOpen={false}>
            {/* Block background  */}
            <label className="components-base-control__label">Background color</label>

            <div className="components-base-control">
              <ButtonGroup>
                {colors.map((item, i) => {
                  return (
                    <Button
                      onClick={() => setAttributes({ backgroundColor: item.slug })}
                      isDefault={backgroundColor !== item.slug}
                      isPrimary={backgroundColor === item.slug}
                      style={{ backgroundColor: item.color }}>
                      &nbsp; &nbsp;
                    </Button>
                  );
                })}
              </ButtonGroup>
            </div>
            <label>Background opacity</label>
            <PlainText
              className="plain-text"
              value={backgroundOpacity}
              onChange={(backgroundOpacity) => setAttributes({ backgroundOpacity })}
            />
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
