const { ButtonGroup, Button } = wp.components;
const { __ } = wp.i18n; 
export default ({
  marginValue,
  onHorizontalMargins,

}) => {
  return (
    <div className="components-base-control">
      <label className="components-base-control__label">Horizontal margin (%)</label>
      <ButtonGroup>
        {[...Array(6)].map((_, i) => {
          return <Button onClick={() => onHorizontalMargins(`0 ${i*4}vw 0 ${i*4}vw`)}
            isDefault={marginValue !== `0 ${i*4}vw 0 ${i*4}vw`}
            isPrimary={marginValue === `0 ${i*4}vw 0 ${i*4}vw`} >{i*4}</Button>
          })}
      </ButtonGroup>
    </div>
  );
}
