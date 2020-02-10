const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
  const el = props.gridLayout[props.selectedElement.componentI];
  return el
    && props.gridMode && props.selectedElement.component.blockName === "coreparagraph"
    || props.selectedElement.component.blockName === "componentwrapper"
    ? <div className="components-base-control">
      <label className="components-base-control__label">Font size</label>
      <ButtonGroup>
        {[
          {
            name: __('XS'),
            slug: 'ultrasmall',
          },
          {
            name: __('S'),
            slug: 'small',
          },
          {
            name: __('M'),
            slug: 'normal',
          },
          {
            name: __('L'),
            slug: 'large',
          },
          {
            name: __('XL'),
            slug: 'huge',
          },
        ].map((item, i) => {
          return <Button onClick={() => props.onChangeFontClassSize(item.slug)}
            isDefault={el.fontSize  !== item.slug}
            isPrimary = { el.fontSize  === item.slug} >{item.name}</Button>
        })}
      </ButtonGroup>
    </div> : null
}
