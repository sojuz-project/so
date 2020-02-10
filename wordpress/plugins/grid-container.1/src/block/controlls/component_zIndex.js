const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n; 
export default ({
  selectedElement,
  onZIndexChange,
  gridLayoutObject: {
    zIndex,
  } = {},
}) => {
  return (
    selectedElement.component.blockName === "coreparagraph"
    || selectedElement.component.blockName === "coreheading"
    || selectedElement.component.blockName === "componentwrapper"
    || selectedElement.component.blockName === "customimage") && (
    <div className="components-base-control">
      <label className="components-base-control__label">Component layer position (z-index)</label>
      <ButtonGroup>
        {[...Array(6)].map((_, i) => {
          return <Button onClick={() => onZIndexChange(i)}
            isDefault={zIndex !== i}
            isPrimary={zIndex === i} >{i}</Button>
          })}
      </ButtonGroup>
    </div>
  );
}
