/**
 * Generates an SVG image.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @requires    ES6
 * 
 * @since   LRS 3.29.2
 * @since   LBF 0.4.0-beta
 */


/**
 * Tool for generating SVG images inline for Javascript.
 * 
 * @property {object} _xml  The xml container that is represented as an `<svg>` tag.
 * 
 * @since   LBF 0.4.0-beta
 */
export default class SVGTool {

    /**
     * Class constructor. Sets property this._xml and the default properties of the SVG file
     * 
     * @since   LBF 0.4.0-beta
     */

    constructor() {
        this._xml = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        this.set_default_attributes();
    }


    /**
     * Return the fully formed SVG image.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    render() {
        return this._xml;
    }


    /**
     * Console.log's the SVG file in whatever state that is is in.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */
    debug() {
        console.log(this._xml);
        return this;
    }


    /**
     * Set any general attribute to the SVG image `<svg>` tag.
     * 
     * @param {string} attribute    The attribute to add.
     * @param {string} value        The value of the attribute to add.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_attribute(attribute, value) {
        this._xml.setAttributeNS(null, attribute, value);
        return this;
    }


    /**
     * Set (or reset) the properties of the `<svg>` tag.
     * 
     * | Attribute | Value          |
     * | --------- | -------------- |
     * | width     | `24`           |
     * | height    | `24`           |
     * | viewBox   | `0 0 24 24`    |
     * | fill      | `none`         |
     * | stroke    | `currentColor` |
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_default_attributes() {
        this.set_width(24);
        this.set_height(24);
        this.set_viewBox('0 0 24 24');
        this.set_fill('none');
        this.set_stroke('currentColor');
        return this;
    }


    /**
     * Set the width of the `<svg>` tag.
     * 
     * @param {string} width    The width of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_width(width) {
        this.set_attribute('width', width);
        return this;
    }


    /**
     * Set the height of the `<svg>` tag.
     * 
     * @param {string} height    The height of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_height(height) {
        this.set_attribute('height', height);
        return this;
    }


    /**
     * Set the viewBox attribute of the `<svg>` tag.
     * 
     * @param {string} viewBox    The viewBox of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_viewBox(viewBox) {
        this.set_attribute('viewBox', viewBox);
        return this;
    }


    /**
     * Set the fill attribute of the `<svg>` tag.
     * 
     * @param {string} fill    The fill of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_fill(fill) {
        this.set_attribute('fill', fill);
        return this;
    }


    /**
     * Set the stroke attribute of the `<svg>` tag.
     * 
     * @param {string} stroke    The stroke of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_stroke(stroke) {
        this.set_attribute('stroke', stroke);
        return this;
    }


    /**
     * Set the stroke-width attribute of the `<svg>` tag.
     * 
     * @param {string} width    The stroke-width of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_stroke_width(width) {
        this.set_attribute('stroke-width', width);
        return this;
    }


    /**
     * Set the stroke-linecap attribute of the `<svg>` tag.
     * 
     * @param {string} linecap    The stroke-linecap of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_stroke_linecap(linecap) {
        this.set_attribute('stroke-linecap', linecap);
        return this;
    }


    /**
     * Set the stroke-linejoin attribute of the `<svg>` tag.
     * 
     * @param {string} linejoin    The stroke-linejoin of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_stroke_linejoin(linejoin) {
        this.set_attribute('stroke-linejoin', linejoin);
        return this;
    }


    /**
     * Add an entry to the class of the `<svg>` tag.
     * 
     * @param {string} class_name    Class item to add to the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    add_class(class_name) {
        this._xml.classList.add(class_name);
        return this;
    }


    /**
     * Remove an entry to the class of the `<svg>` tag.
     * 
     * @param {string} class_name    Class item to remove to the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    remove_class(class_name) {
        this._xml.classList.remove(class_name);
        return this;
    }


    /**
     * Set the id attribute of the `<svg>` tag.
     * 
     * @param {string} id    The id of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_id(id) {
        this.set_attribute('id', id);
        return this;
    }


    /**
     * Set the name attribute of the `<svg>` tag.
     * 
     * @param {string} name    The name of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_name(name) {
        this.set_attribute('name', name);
        return this;
    }


    /**
     * Set the href attribute of the `<svg>` tag.
     * 
     * @param {string} href    The href of the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_href(href) {
        this.set_attribute('href', href);
        return this;
    }


    /**
     * Add a `data-customAttribute` to the `<svg>` tag.
     * 
     * @param {string} key    The data key of the element to add to the `<svg>` tag.
     * @param {string} data   The value of the element to add to the `<svg>` tag.
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    add_to_dataset(key, data) {
        this.set_attribute(`data-${key}`, data);
        return this;
    }


    /**
     * Add a circle to the SVG image.
     * 
     * @param {string} cx       The x coordinate of the center of the circle. 
     * @param {string} cy       The r coordinate of the center of the circle.
     * @param {string} r        The radius of the circle.
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_circle.asp
     * @since   LBF 0.4.0-beta
     */

    add_circle(cx, cy, r, params = {}) {
        const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        circle.setAttributeNS(null, 'cx', cx);
        circle.setAttributeNS(null, 'cy', cy);
        circle.setAttributeNS(null, 'r', r);
        for (const key in params) {
            const val = params[key];
            circle.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(circle);
        return this;
    }


    /**
     * Add a line to an SVG image.
     * 
     * @param {string} x1       The x1 attribute defines the start of the line on the x-axis.
     * @param {string} y1       The y1 attribute defines the start of the line on the y-axis.
     * @param {string} x2       The x2 attribute defines the end of the line on the x-axis.
     * @param {string} y2       The y2 attribute defines the end of the line on the y-axis.
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_line.asp
     * @since   LBF 0.4.0-beta
     */

    add_line(x1, y1, x2, y2, params = {}) {
        const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
        line.setAttributeNS(null, 'x1', x1);
        line.setAttributeNS(null, 'y1', y1);
        line.setAttributeNS(null, 'x2', x2);
        line.setAttributeNS(null, 'y2', y2);
        for (const key in params) {
            const val = params[key];
            line.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(line);
        return this;
    }


    /**
     * Add a rectangle to the SVG image.
     * 
     * @param {string} x        The x attribute defines the left position of the rectangle (e.g. x="50" places the rectangle 50 px from the left margin)
     * @param {string} y        The y attribute defines the top position of the rectangle (e.g. y="20" places the rectangle 20 px from the top margin)
     * @param {string} width    The width of the rectangle.
     * @param {string} height   The height of the rectangle.
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_rect.asp
     * @since   LBF 0.4.0-beta
     */

    add_rect(x, y, width, height, params = {}) {
        const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
        rect.setAttributeNS(null, 'x', x);
        rect.setAttributeNS(null, 'y', y);
        rect.setAttributeNS(null, 'width', width);
        rect.setAttributeNS(null, 'height', height);
        for (const key in params) {
            const val = params[key];
            rect.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(rect);
        return this;
    }


    /**
     * Add a path element, a very raw image to the SVG image.
     * 
     * ## Path Info
     * 
     * The following commands are available for path data:
     * 
     * | Key | Explanation                     |
     * | --- | ------------------------------- |
     * | M   | moveto                          |
     * | L   | lineto                          |
     * | H   | horizontal lineto               |
     * | V   | vertical lineto                 |
     * | C   | curveto                         |
     * | S   | smooth curveto                  |
     * | Q   | quadratic Bézier curve          |
     * | T   | smooth quadratic Bézier curveto |
     * | A   | elliptical Arc                  |
     * | Z   | closepath                       |
     * 
     * **Note:** All of the commands above can also be expressed with lower letters. Capital letters means absolutely positioned, lower cases means relatively positioned.
     * 
     * @param {string} d        Defined path to draw onto the SVG image.
     *                          Example: `M150 0 L75 200 L225 200 Z`
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_path.asp
     * @since   LBF 0.4.0-beta
     */

    add_path(d, params = {}) {
        const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
        path.setAttributeNS(null, 'd', d);
        for (const key in params) {
            const val = params[key];
            path.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(path);
        return this;
    }


    /**
     * Add a polyline to an SVG image.
     * 
     * @param {string} points   The points attribute defines the list of points (pairs of x and y coordinates) required to draw the polyline.
     *                          Example: `20,20 40,25 60,40 80,120 120,140 200,180`
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_polyline.asp
     * @since   LBF 0.4.0-beta
     */

    add_polyline(points, params = {}) {
        const polyline = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
        polyline.setAttributeNS(null, 'points', points);
        for (const key in params) {
            const val = params[key];
            polyline.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(polyline);
        return this;
    }


    /**
     * Add an ellipse to an SVG image.
     * 
     * @param {string} cx       The cx attribute defines the x coordinate of the center of the ellipse.
     * @param {string} cy       The cy attribute defines the y coordinate of the center of the ellipse.
     * @param {string} rx       The rx attribute defines the horizontal radius.
     * @param {string} ry       The ry attribute defines the vertical radius.
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_ellipse.asp
     * @since   LBF 0.4.0-beta
     */

    add_ellipse(cx, cy, rx, ry, params = {}) {
        const ellipse = document.createElementNS("http://www.w3.org/2000/svg", "ellipse");
        ellipse.setAttributeNS(null, 'cx', cx);
        ellipse.setAttributeNS(null, 'cy', cy);
        ellipse.setAttributeNS(null, 'rx', rx);
        ellipse.setAttributeNS(null, 'ry', ry);
        for (const key in params) {
            const val = params[key];
            ellipse.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(ellipse);
        return this;
    }


    /**
     * Add a polygon into the SVG.
     * 
     * @param {string} points   The points attribute defines the x and y coordinates for each corner of the polygon.
     *                          Example: `200,10 250,190 160,210`
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_polygon.asp
     * @since   LBF 0.4.0-beta
     */

    add_polygon(points, params = {}) {
        const polygon = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
        polygon.setAttributeNS(null, 'points', points);
        for (const key in params) {
            const val = params[key];
            polygon.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(polygon);
        return this;
    }


    /**
     * Add a text element into the SVG.
     * 
     * @param {string} x        The x coordinate of the text item.
     * @param {string} y        The y coordinate of the text item.
     * @param {string} text     The text to print into the SVG.
     * @param {object} params   Any additional params to add to the element, like `stroke`, `stroke-width` or `fill` etc.
     *                          Default is `{}`
     * 
     * @returns {static}
     * 
     * @see     https://www.w3schools.com/graphics/svg_text.asp
     * @since   LBF 0.4.0-beta
     */

    add_text(x, y, text, params = {}) {
        const text_element = document.createElementNS("http://www.w3.org/2000/svg", "text");
        text_element.setAttributeNS(null, "x", x);
        text_element.setAttributeNS(null, "y", y);
        text_element.innerHTML = text;
        for (const key in params) {
            const val = params[key];
            text_element.setAttributeNS(null, key, val);
        }
        this._xml.appendChild(text_element);
        return this;
    }


    /**
     * 
     * 
     * @returns {static}
     * 
     * @since   LBF 0.4.0-beta
     */

    set_on_function(type, fn) {
        this._xml.addEventListener(type, fn);
        return this;
    }

}