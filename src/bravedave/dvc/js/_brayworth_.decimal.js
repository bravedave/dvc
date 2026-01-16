/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 * this is my attempt to avoid floating point errors
 *
 * https://stackoverflow.com/questions/1458633/how-to-deal-with-floating-point-number-precision-in-javascript
 * https://stackoverflow.com/questions/588004/is-floating-point-math-broken
 * https://stackoverflow.com/questions/28045787/how-many-decimal-places-does-the-primitive-float-and-double-support
 *
 * just add and sub at this stage - needs multiply and divide
 *
 * let t = decimal(0,2);  // 2 decimal places, multiple
 *                        // everything by 100 and make it an integer
 * console.log(t.add(.1).add(.2).value()); // .3
 */
(_ => {
  _.decimal = (value = 0, decimals = 2) => {
    /**
     * max scale is 20 decimal places
     * the default scale is 100 (2 decimals, Math.pow(10,2) == 100)
     */
    if (Number(decimals) > 20) {
      throw new Error('Max Decimals is 20');
    }
    let scale = parseInt(Math.pow(10, !!decimals ? parseInt(decimals) : 2));

    return {

      scale: scale,
      _value: parseInt((Number(!!value ? value : 0) * scale).toPrecision(15)),

      add: function (v) {

        this._value += parseInt((Number(v) * this.scale).toPrecision(15));
        return this; // chain
      },

      asLocaleCurrency: function (fractions) {

        return _.asLocaleCurrency(this.value(), fractions);
      },

      asLocaleNumber: function (fractions) {

        return _.asLocaleNumber(this.value(), fractions);
      },

      sub: function (v) {

        this._value -= parseInt((Number(v) * this.scale).toPrecision(15));
        return this; // chain
      },

      value: function (decimals) {

        let r = parseInt(this._value) / this.scale;
        return !!decimals ? _.decimal(r, decimals).value() : r;
      }
    };
  };
})(_brayworth_);
