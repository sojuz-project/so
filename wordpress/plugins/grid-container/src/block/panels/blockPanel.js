const { PanelBody, PanelRow, ButtonGroup, Button } = wp.components;
const { ToggleControl } = wp.components;
const { __ } = wp.i18n;

import RangeControll from '../controlls/range_controll';
import ButtonsColorControll from '../controlls/buttons_color_controll';
import { colorsSchema } from '../schemas/colors'
import {
  BLOCK,
  CLASS,
  TECHNICAL,
} from '../index'

export default ({
  classBlock,
  technicalBlock,
  onUpdate,
  addSection,
  removeSection,
}) => {
  const parseStyle = (attrs) => {
    // This is attrs transforms
    const technicalBlock = attrs.technical.block
    Object.keys(attrs.technical.block).map(param => {
      switch(param) {
        case 'gridTemplateColumns':
          // updated.style.component[selectedElement.componentI].height = `${calcCompH(val[selectedElement.componentI].h)}vw`
          attrs.style.block.gridTemplateColumns = `repeat(${technicalBlock.gridTemplateColumns}, 1fr)`
          break;
        case 'padding':
          attrs.style.block.padding = `0 ${technicalBlock.padding}vw`
          break;
        case 'gridGap':
          attrs.style.block.gridGap = `${technicalBlock.gridGap}vw`
          break;
      }
    })
    return attrs
  }
  return (
    <PanelBody
      title={__('Block properties')}
      initialOpen={false}
    >
      <PanelRow/>
      <ButtonsColorControll
        label="Background color"
        onUpdate={onUpdate(`${CLASS}.${BLOCK}.background-color`)}
        value={classBlock['background-color']}
        data={colorsSchema} />
      <RangeControll
        label="Horizontal paddings"
        onUpdate={onUpdate(`${TECHNICAL}.${BLOCK}.padding`, parseStyle)}
        value={technicalBlock.padding}
        range={[0, 10, 1, /(\d+\s)(\d+)([a-z]+)/ ,1]} />
        <hr/>
      <RangeControll
        label="Columns"
        onUpdate={onUpdate(`${TECHNICAL}.${BLOCK}.gridTemplateColumns`, parseStyle)}
        value={technicalBlock.gridTemplateColumns}
        range={[1, 10, 1, /(repeat\()(\d+)(, \d+[a-z]+\))/ ,1]} />
      <RangeControll
        label="Section grid gap"
        onUpdate={onUpdate(`${TECHNICAL}.${BLOCK}.gridGap`, parseStyle)}
        value={technicalBlock.gridGap}
        range={[0, 5, .5, /(repeat\()(\d+)(, \d+[a-z]+\))/, 1]} />
      <hr />


      <ToggleControl label="Display as slider"
        checked={technicalBlock.isSlider}
        onChange={onUpdate(`${TECHNICAL}.${BLOCK}.isSlider`)}
      /> 
      <ToggleControl label="Pagination"
        checked={technicalBlock.pagination}
        onChange={onUpdate(`${TECHNICAL}.${BLOCK}.pagination`)}
      /> 

      <div className="components-base-control">
        <label className="components-base-control__label">Duplicate sections</label>
        <ButtonGroup>
          <Button isDefault onClick={removeSection}>
            remove section
          </Button>
          <Button isPrimary onClick={addSection}>
            add section
          </Button>
        </ButtonGroup>
      </div>
       
     
    </PanelBody>
  );
}
