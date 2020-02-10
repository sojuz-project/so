/* 
TEST            -> REGEX   range[3]                         -> GROUP range[4]
0 2 vw          -> (\d+\s)(\d+)([a-z]+)                     -> 1
2vw             -> (\d+)([a-z]+)                            -> 0
repeat(2,2fr)   -> (repeat\()(\d+)(,\d+[a-z]+\))             -> 1
calc(2 - 200%)  -> (calc\()(\d+)( - \d+%\))                 -> 1
*/
const { RangeControl } = wp.components;
const { __ } = wp.i18n;
export default (props) => {
  // let parsed
  // const praseInVal = (value) => {
  //   const val = '' + value;
  //   parsed = val.match(props.range[3]) 
  //   parsed.shift()
  //   console.log('>>',parsed)
  //   return parseInt(parsed[1])
  // }
  // const praseOutVal = (value) => {
  //   const newParsed = parsed
  //   newParsed[1] = value
  //   return newParsed.join('')
  // }

   return (
      <RangeControl
        label={props.label}
        value={props.value}
        onChange={value => props.onUpdate(value)}
        min={props.range[0]}
        max={props.range[1]}
        step={props.range[2]}
      />)
}
