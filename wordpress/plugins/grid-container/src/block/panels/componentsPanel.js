const { PanelBody, PanelRow, TextControl } = wp.components;
const { __ } = wp.i18n;
import ButtonsControll from './../controlls/buttons_controll';
import ButtonsColorControll from './../controlls/buttons_color_controll';
import SelectControll from './../controlls/select_controll';
import RangeControll from '../controlls/range_controll';
import { headingsSchema } from './../schemas/headings'
import { colorsSchema } from './../schemas/colors'
import { numbersSchema } from './../schemas/numbers'
import { textSizesSchema } from './../schemas/textSizes'
import { opacitySchema } from './../schemas/opacity'
import { weightsSchema } from './../schemas/weights'
import {
  TECHNICAL,
  CLASS,
  STYLE,
  COMPONENT,
} from '../index'

export default ({
  technicalComponent,
  classComponent,
  styleComponent,
  componentI,
  onUpdate,
}) => {
  const parseStyle = (attrs) => {
    // This is attrs transforms
    const technicalComponent = attrs.technical.component[componentI]
    Object.keys(technicalComponent).map(param => {
      switch (param) {
        case 'padding':
          attrs.style.component[componentI].padding = `${technicalComponent.padding}vw`
          break;
      }
    })
    return attrs
  }
  return (
    <PanelBody
      title={__('Component properties')}
      initialOpen={true}
    >
      <PanelRow/>
      <ButtonsControll
        label="Headings"
        onUpdate={onUpdate(`${TECHNICAL}.${COMPONENT}[${componentI}].tagName`)}
        value={technicalComponent.tagName}
        data={headingsSchema} />
      <ButtonsControll
        label="Text size"
        onUpdate={onUpdate(`${CLASS}.${COMPONENT}[${componentI}]['font-size']`)}
        value={classComponent['font-size']}
        data={textSizesSchema} />
      <hr />
      <RangeControll
        label="Inner padding"
        onUpdate={onUpdate(`${TECHNICAL}.${COMPONENT}[${componentI}].padding`, parseStyle)}
        value={technicalComponent.padding}
        range={[0, 10, .25, /(\d+\s)(\d+)([a-z]+)/, 1]} />
      <ButtonsColorControll
        label="Text color"
        onUpdate={onUpdate(`${CLASS}.${COMPONENT}[${componentI}].color`)}
        value={classComponent.color}
        data={colorsSchema} />
      <ButtonsColorControll
        label="Background color"
        onUpdate={onUpdate(`${CLASS}.${COMPONENT}[${componentI}].background-color`)}
        value={classComponent['background-color']}
        data={colorsSchema} />
      <SelectControll
        label="Background opacity"
        onUpdate={onUpdate(`${CLASS}.${COMPONENT}[${componentI}].background-opacity`)}
        value={classComponent['background-opacity']}
        data={opacitySchema} />
      <ButtonsControll
        label="Z index"
        onUpdate={onUpdate(`${STYLE}.${COMPONENT}[${componentI}].zIndex`)}
        value={styleComponent['zIndex']}
        data={numbersSchema} />
      <SelectControll
        label="Font weight"
        onUpdate={onUpdate(`${CLASS}.${COMPONENT}[${componentI}].fontWeight`)}
        value={classComponent['fontWeight']}
        data={weightsSchema} />
      <TextControl
        label="Reasign TO"
        value={technicalComponent.reasignTo}
        onChange={onUpdate(`${TECHNICAL}.${COMPONENT}[${componentI}].reasignTo`)}
      />

    </PanelBody>
  );
}
