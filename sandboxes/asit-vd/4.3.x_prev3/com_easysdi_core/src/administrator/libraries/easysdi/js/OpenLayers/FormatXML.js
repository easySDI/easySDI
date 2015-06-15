//Fix for IE11 bug with OpenLayers GetFeature request
var _class = OpenLayers.Format.XML;
var originalWriteFunction = _class.prototype.write;
var patchedWriteFunction = function()
{
        var child = originalWriteFunction.apply( this, arguments );

        // NOTE: Remove the rogue namespaces as one block of text.
        //       The second fragment "NS1:" is too small on its own and could cause valid text (in, say, ogc:Literal elements) to be erroneously removed.
        child = child.replace( new RegExp( 'xmlns:NS1="" NS1:', 'g' ), '' );

        return child;
}
_class.prototype.write = patchedWriteFunction;