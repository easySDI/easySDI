var DFXGlobalHandle = 2000;

function downloadPerimeter(format, orderId) {
    var layer;

    if (polygonLayer) {
        layer = polygonLayer;
    }
    else if (selectLayer) {
        layer = selectLayer;
    } else {
        return false;
    }

    switch (format) {
        case "DXF":
            getDXFFileFromFeatureList(orderId, layer)
            break;
        case "KML":
            getKMLFileFromFeatureList(orderId, layer)
            break;
        case "GML":
            getGMLFileFromFeatureList(orderId, layer);
            break;
        case "GeoJSON":
            getGeoJSONFileFromFeatureList(orderId, layer);
            break;            
    }
    return false;
}

function getKMLFileFromFeatureList(orderId, layer) {
    var srsName = appname.mapPanel.map.getProjection();

    var format = new OpenLayers.Format.KML({
        maxDepth: 10,
        extractStyles: true,
        internalProjection: new OpenLayers.Projection(srsName),
        externalProjection: new OpenLayers.Projection("EPSG:4326"),
        placemarksDesc: orderId,
        foldersName: orderId,
        foldersDesc: orderId
    });
    saveTextAs(format.write(layer.features), orderId + ".kml");
}

function getGeoJSONFileFromFeatureList(orderId, layer) {
    var srsName = appname.mapPanel.map.getProjection();

    var format = new OpenLayers.Format.GeoJSON({});
    saveTextAs(format.write(layer.features), orderId + ".geojson");
}

function getGMLFileFromFeatureList(orderId, layer) {
    var srsName = appname.mapPanel.map.getProjection();
    var gmlOptions = {
        featureType: "feature",
        featureNS: "http://asitvd.ch/feature",
        srsName: srsName
    };

    var gmlOptionsIn = OpenLayers.Util.extend(
            OpenLayers.Util.extend({}, gmlOptions)
            );

    var format = new OpenLayers.Format.GML.v2(gmlOptionsIn);
    saveTextAs(format.write(layer.features), orderId + ".gml");
}

function getDXFFileFromFeatureList(orderId, layer) {
    var bbox = layer.getDataExtent();
    var rings;
    var rCount;
    rings = getLinearRingsFromFeatures(layer.features);
    saveTextAs(buildDXFTemplate(bbox, rings), orderId + ".dxf");
}

function getLinearRingsFromFeatures(features) {
    var featuresCount = features.length;
    var rings = [];

    for (var i = 0; i < featuresCount; ++i) {
        var feature = features[i];
        if (feature.geometry instanceof OpenLayers.Geometry.MultiPolygon) {

            for (var j = 0; j < feature.geometry.components.length; ++j) {
                rings = rings.concat(feature.geometry.components[j].components);
            }

        }
        else if (feature.geometry instanceof OpenLayers.Geometry.Polygon) {
            rings = rings.concat(feature.geometry.components);
        }
    }
    return(rings);
}

function dechex(number) {
    //  discuss at: http://phpjs.org/functions/dechex/
    // original by: Philippe Baumann
    // bugfixed by: Onno Marsman
    // improved by: http://stackoverflow.com/questions/57803/how-to-convert-decimal-to-hex-in-javascript
    //    input by: pilus
    //   example 1: dechex(10);
    //   returns 1: 'a'
    //   example 2: dechex(47);
    //   returns 2: '2f'
    //   example 3: dechex(-1415723993);
    //   returns 3: 'ab9dc427'

    if (number < 0) {
        number = 0xFFFFFFFF + number + 1;
    }
    return parseInt(number, 10)
            .toString(16);
}

function buildDXPolyline(ring) {
    DFXGlobalHandle++
    var pointsCount = ring.components.length;
    var DXFPoly = '  0\n' +
            'POLYLINE\n' +
            '  5\n' +
            ''+dechex(DFXGlobalHandle)+'\n' +
            '  8\n' +
            '0\n' +
            ' 66\n' +
            '     1\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            ' 70\n' +
            '1\n';

    for (var i = 0; i < pointsCount; ++i) {
        DFXGlobalHandle++;

        DXFPoly += '  0\n' +
                'VERTEX\n' +
                '  5\n' +
                '' + dechex(DFXGlobalHandle).toUpperCase() + '\n' +
                '  8\n' +
                '0\n' +
                ' 10\n' +
                '' + ring.components[i].x + '\n' +
                ' 20\n' +
                '' + ring.components[i].y + '\n' +
                ' 30\n' +
                '0.0\n' +
                '';
    }

    DFXGlobalHandle++
    DXFPoly += '  0\n' +
            'SEQEND\n' +
            '  5\n' +
            ''+dechex(DFXGlobalHandle)+'\n' +
            '  8\n' +
            '0\n';

    return DXFPoly;

}

function buildDXFTemplate(bbox, rings) {
    var polygons = '';
    for (var i = 0; i < rings.length; ++i) {
        polygons += buildDXPolyline(rings[i]);
    }

    return '  0\n' +
            'SECTION\n' +
            '  2\n' +
            'HEADER\n' +
            '  9\n' +
            '$ACADVER\n' +
            '  1\n' +
            'AC1009\n' +
            '  9\n' +
            '$INSBASE\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$EXTMIN\n' +
            ' 10\n' +
            '' + bbox.left + '\n' +
            ' 20\n' +
            '' + bbox.bottom + '\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$EXTMAX\n' +
            ' 10\n' +
            '' + bbox.right + '\n' +
            ' 20\n' +
            '' + bbox.top + '\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$LIMMIN\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            '  9\n' +
            '$LIMMAX\n' +
            ' 10\n' +
            '420.0\n' +
            ' 20\n' +
            '297.0\n' +
            '  9\n' +
            '$ORTHOMODE\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$REGENMODE\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$FILLMODE\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$QTEXTMODE\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$MIRRTEXT\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DRAGMODE\n' +
            ' 70\n' +
            '     2\n' +
            '  9\n' +
            '$LTSCALE\n' +
            ' 40\n' +
            '1.0\n' +
            '  9\n' +
            '$OSMODE\n' +
            ' 70\n' +
            '    37\n' +
            '  9\n' +
            '$ATTMODE\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$TEXTSIZE\n' +
            ' 40\n' +
            '2.5\n' +
            '  9\n' +
            '$TRACEWID\n' +
            ' 40\n' +
            '1.0\n' +
            '  9\n' +
            '$TEXTSTYLE\n' +
            '  7\n' +
            'STANDARD\n' +
            '  9\n' +
            '$CLAYER\n' +
            '  8\n' +
            '0\n' +
            '  9\n' +
            '$CELTYPE\n' +
            '  6\n' +
            'BYLAYER\n' +
            '  9\n' +
            '$CECOLOR\n' +
            ' 62\n' +
            '   256\n' +
            '  9\n' +
            '$DIMSCALE\n' +
            ' 40\n' +
            '1.0\n' +
            '  9\n' +
            '$DIMASZ\n' +
            ' 40\n' +
            '2.5\n' +
            '  9\n' +
            '$DIMEXO\n' +
            ' 40\n' +
            '0.625\n' +
            '  9\n' +
            '$DIMDLI\n' +
            ' 40\n' +
            '3.75\n' +
            '  9\n' +
            '$DIMRND\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$DIMDLE\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$DIMEXE\n' +
            ' 40\n' +
            '1.25\n' +
            '  9\n' +
            '$DIMTP\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$DIMTM\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$DIMTXT\n' +
            ' 40\n' +
            '2.5\n' +
            '  9\n' +
            '$DIMCEN\n' +
            ' 40\n' +
            '2.5\n' +
            '  9\n' +
            '$DIMTSZ\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$DIMTOL\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMLIM\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMTIH\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMTOH\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMSE1\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMSE2\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMTAD\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$DIMZIN\n' +
            ' 70\n' +
            '     8\n' +
            '  9\n' +
            '$DIMBLK\n' +
            '  1\n' +
            '\n' +
            '  9\n' +
            '$DIMASO\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$DIMSHO\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$DIMPOST\n' +
            '  1\n' +
            '\n' +
            '  9\n' +
            '$DIMAPOST\n' +
            '  1\n' +
            '\n' +
            '  9\n' +
            '$DIMALT\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMALTD\n' +
            ' 70\n' +
            '     3\n' +
            '  9\n' +
            '$DIMALTF\n' +
            ' 40\n' +
            '0.03937007874016\n' +
            '  9\n' +
            '$DIMLFAC\n' +
            ' 40\n' +
            '1.0\n' +
            '  9\n' +
            '$DIMTOFL\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$DIMTVP\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$DIMTIX\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMSOXD\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMSAH\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMBLK1\n' +
            '  1\n' +
            '\n' +
            '  9\n' +
            '$DIMBLK2\n' +
            '  1\n' +
            '\n' +
            '  9\n' +
            '$DIMSTYLE\n' +
            '  2\n' +
            'ISO-25\n' +
            '  9\n' +
            '$DIMCLRD\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMCLRE\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMCLRT\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$DIMTFAC\n' +
            ' 40\n' +
            '1.0\n' +
            '  9\n' +
            '$DIMGAP\n' +
            ' 40\n' +
            '0.625\n' +
            '  9\n' +
            '$LUNITS\n' +
            ' 70\n' +
            '     2\n' +
            '  9\n' +
            '$LUPREC\n' +
            ' 70\n' +
            '     4\n' +
            '  9\n' +
            '$SKETCHINC\n' +
            ' 40\n' +
            '1.0\n' +
            '  9\n' +
            '$FILLETRAD\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$AUNITS\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$AUPREC\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$MENU\n' +
            '  1\n' +
            '.\n' +
            '  9\n' +
            '$ELEVATION\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$PELEVATION\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$THICKNESS\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$LIMCHECK\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$BLIPMODE\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$CHAMFERA\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$CHAMFERB\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$SKPOLY\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$TDCREATE\n' +
            ' 40\n' +
            '2456337.6735687042\n' +
            '  9\n' +
            '$TDUPDATE\n' +
            ' 40\n' +
            '2456337.6787776388\n' +
            '  9\n' +
            '$TDINDWG\n' +
            ' 40\n' +
            '0.0052466782\n' +
            '  9\n' +
            '$TDUSRTIMER\n' +
            ' 40\n' +
            '0.0052157986\n' +
            '  9\n' +
            '$USRTIMER\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$ANGBASE\n' +
            ' 50\n' +
            '0.0\n' +
            '  9\n' +
            '$ANGDIR\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$PDMODE\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$PDSIZE\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$PLINEWID\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$COORDS\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$SPLFRAME\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$SPLINETYPE\n' +
            ' 70\n' +
            '     6\n' +
            '  9\n' +
            '$SPLINESEGS\n' +
            ' 70\n' +
            '     8\n' +
            '  9\n' +
            '$ATTDIA\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$ATTREQ\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$HANDLING\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$HANDSEED\n' +
            '  5\n' +
            '381\n' +
            '  9\n' +
            '$SURFTAB1\n' +
            ' 70\n' +
            '     6\n' +
            '  9\n' +
            '$SURFTAB2\n' +
            ' 70\n' +
            '     6\n' +
            '  9\n' +
            '$SURFTYPE\n' +
            ' 70\n' +
            '     6\n' +
            '  9\n' +
            '$SURFU\n' +
            ' 70\n' +
            '     6\n' +
            '  9\n' +
            '$SURFV\n' +
            ' 70\n' +
            '     6\n' +
            '  9\n' +
            '$UCSNAME\n' +
            '  2\n' +
            '\n' +
            '  9\n' +
            '$UCSORG\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$UCSXDIR\n' +
            ' 10\n' +
            '1.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$UCSYDIR\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '1.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$PUCSNAME\n' +
            '  2\n' +
            '\n' +
            '  9\n' +
            '$PUCSORG\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$PUCSXDIR\n' +
            ' 10\n' +
            '1.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$PUCSYDIR\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '1.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$USERI1\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$USERI2\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$USERI3\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$USERI4\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$USERI5\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$USERR1\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$USERR2\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$USERR3\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$USERR4\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$USERR5\n' +
            ' 40\n' +
            '0.0\n' +
            '  9\n' +
            '$WORLDVIEW\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$SHADEDGE\n' +
            ' 70\n' +
            '     3\n' +
            '  9\n' +
            '$SHADEDIF\n' +
            ' 70\n' +
            '    70\n' +
            '  9\n' +
            '$TILEMODE\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$MAXACTVP\n' +
            ' 70\n' +
            '    64\n' +
            '  9\n' +
            '$PLIMCHECK\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$PEXTMIN\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$PEXTMAX\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  9\n' +
            '$PLIMMIN\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            '  9\n' +
            '$PLIMMAX\n' +
            ' 10\n' +
            '12.0\n' +
            ' 20\n' +
            '9.0\n' +
            '  9\n' +
            '$UNITMODE\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$VISRETAIN\n' +
            ' 70\n' +
            '     1\n' +
            '  9\n' +
            '$PLINEGEN\n' +
            ' 70\n' +
            '     0\n' +
            '  9\n' +
            '$PSLTSCALE\n' +
            ' 70\n' +
            '     1\n' +
            '  0\n' +
            'ENDSEC\n' +
            '  0\n' +
            'SECTION\n' +
            '  2\n' +
            'TABLES\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'VPORT\n' +
            ' 70\n' +
            '     1\n' +
            '  0\n' +
            'VPORT\n' +
            '  2\n' +
            '*ACTIVE\n' +
            ' 70\n' +
            '     0\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 11\n' +
            '1.0\n' +
            ' 21\n' +
            '1.0\n' +
            ' 12\n' +
            '' + (bbox.left + bbox.right) / 2 + '\n' +
            ' 22\n' +
            '' + (bbox.bottom + bbox.top) / 2 + '\n' +
            ' 13\n' +
            '0.0\n' +
            ' 23\n' +
            '0.0\n' +
            ' 14\n' +
            '10.0\n' +
            ' 24\n' +
            '10.0\n' +
            ' 15\n' +
            '10.0\n' +
            ' 25\n' +
            '10.0\n' +
            ' 16\n' +
            '0.0\n' +
            ' 26\n' +
            '0.0\n' +
            ' 36\n' +
            '1.0\n' +
            ' 17\n' +
            '0.0\n' +
            ' 27\n' +
            '0.0\n' +
            ' 37\n' +
            '0.0\n' +
            ' 40\n' +
            '1012.684627994895\n' +
            ' 41\n' +
            '2.303519061583577\n' +
            ' 42\n' +
            '50.0\n' +
            ' 43\n' +
            '0.0\n' +
            ' 44\n' +
            '0.0\n' +
            ' 50\n' +
            '0.0\n' +
            ' 51\n' +
            '0.0\n' +
            ' 71\n' +
            '     0\n' +
            ' 72\n' +
            '  1000\n' +
            ' 73\n' +
            '     1\n' +
            ' 74\n' +
            '     3\n' +
            ' 75\n' +
            '     0\n' +
            ' 76\n' +
            '     0\n' +
            ' 77\n' +
            '     0\n' +
            ' 78\n' +
            '     0\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'LTYPE\n' +
            ' 70\n' +
            '     2\n' +
            '  0\n' +
            'LTYPE\n' +
            '  2\n' +
            'CONTINUOUS\n' +
            ' 70\n' +
            '     0\n' +
            '  3\n' +
            'Solid line\n' +
            ' 72\n' +
            '    65\n' +
            ' 73\n' +
            '     0\n' +
            ' 40\n' +
            '0.0\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'LAYER\n' +
            ' 70\n' +
            '     1\n' +
            '  0\n' +
            'LAYER\n' +
            '  2\n' +
            '0\n' +
            ' 70\n' +
            '     0\n' +
            ' 62\n' +
            '     7\n' +
            '  6\n' +
            'CONTINUOUS\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'STYLE\n' +
            ' 70\n' +
            '     3\n' +
            '  0\n' +
            'STYLE\n' +
            '  2\n' +
            'STANDARD\n' +
            ' 70\n' +
            '     0\n' +
            ' 40\n' +
            '0.0\n' +
            ' 41\n' +
            '1.0\n' +
            ' 50\n' +
            '0.0\n' +
            ' 71\n' +
            '     0\n' +
            ' 42\n' +
            '2.5\n' +
            '  3\n' +
            'txt\n' +
            '  4\n' +
            '\n' +
            '  0\n' +
            'STYLE\n' +
            '  2\n' +
            'ANNOTATIF\n' +
            ' 70\n' +
            '     0\n' +
            ' 40\n' +
            '0.0\n' +
            ' 41\n' +
            '1.0\n' +
            ' 50\n' +
            '0.0\n' +
            ' 71\n' +
            '     0\n' +
            ' 42\n' +
            '2.5\n' +
            '  3\n' +
            'txt\n' +
            '  4\n' +
            '\n' +
            '  0\n' +
            'STYLE\n' +
            '  2\n' +
            'LEGEND\n' +
            ' 70\n' +
            '     0\n' +
            ' 40\n' +
            '0.0\n' +
            ' 41\n' +
            '1.0\n' +
            ' 50\n' +
            '0.0\n' +
            ' 71\n' +
            '     0\n' +
            ' 42\n' +
            '2.5\n' +
            '  3\n' +
            'txt\n' +
            '  4\n' +
            '\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'VIEW\n' +
            ' 70\n' +
            '     1\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'UCS\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'APPID\n' +
            ' 70\n' +
            '    15\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD_PSEXT\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACADANNOPO\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACADANNOTATIVE\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD_DSTYLE_DIMJAG\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD_DSTYLE_DIMTALN\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD_MLEADERVER\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD_NAV_VCDISPLAY\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACMAPDMDISPLAYSTYLEREGAPP\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ADE\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'DCO15\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ADE_PROJECTION\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'MAPGWS\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'ACAD_EXEMPT_FROM_CAD_STANDARDS\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'APPID\n' +
            '  2\n' +
            'MapManagementAppName\n' +
            ' 70\n' +
            '     0\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'TABLE\n' +
            '  2\n' +
            'DIMSTYLE\n' +
            ' 70\n' +
            '     4\n' +
            '  0\n' +
            'DIMSTYLE\n' +
            '  2\n' +
            'STANDARD\n' +
            ' 70\n' +
            '     0\n' +
            '  3\n' +
            '\n' +
            '  4\n' +
            '\n' +
            '  5\n' +
            '\n' +
            '  6\n' +
            '\n' +
            '  7\n' +
            '\n' +
            ' 40\n' +
            '1.0\n' +
            ' 41\n' +
            '0.18\n' +
            ' 42\n' +
            '0.0625\n' +
            ' 43\n' +
            '0.38\n' +
            ' 44\n' +
            '0.18\n' +
            ' 45\n' +
            '0.0\n' +
            ' 46\n' +
            '0.0\n' +
            ' 47\n' +
            '0.0\n' +
            ' 48\n' +
            '0.0\n' +
            '140\n' +
            '0.18\n' +
            '141\n' +
            '0.09\n' +
            '142\n' +
            '0.0\n' +
            '143\n' +
            '25.399999999999999\n' +
            '144\n' +
            '1.0\n' +
            '145\n' +
            '0.0\n' +
            '146\n' +
            '1.0\n' +
            '147\n' +
            '0.09\n' +
            ' 71\n' +
            '     0\n' +
            ' 72\n' +
            '     0\n' +
            ' 73\n' +
            '     1\n' +
            ' 74\n' +
            '     1\n' +
            ' 75\n' +
            '     0\n' +
            ' 76\n' +
            '     0\n' +
            ' 77\n' +
            '     0\n' +
            ' 78\n' +
            '     0\n' +
            '170\n' +
            '     0\n' +
            '171\n' +
            '     2\n' +
            '172\n' +
            '     0\n' +
            '173\n' +
            '     0\n' +
            '174\n' +
            '     0\n' +
            '175\n' +
            '     0\n' +
            '176\n' +
            '     0\n' +
            '177\n' +
            '     0\n' +
            '178\n' +
            '     0\n' +
            '  0\n' +
            'DIMSTYLE\n' +
            '  2\n' +
            'ANNOTATIF\n' +
            ' 70\n' +
            '     0\n' +
            '  3\n' +
            '\n' +
            '  4\n' +
            '\n' +
            '  5\n' +
            '\n' +
            '  6\n' +
            '\n' +
            '  7\n' +
            '\n' +
            ' 40\n' +
            '0.0\n' +
            ' 41\n' +
            '2.5\n' +
            ' 42\n' +
            '0.625\n' +
            ' 43\n' +
            '3.75\n' +
            ' 44\n' +
            '1.25\n' +
            ' 45\n' +
            '0.0\n' +
            ' 46\n' +
            '0.0\n' +
            ' 47\n' +
            '0.0\n' +
            ' 48\n' +
            '0.0\n' +
            '140\n' +
            '2.5\n' +
            '141\n' +
            '2.5\n' +
            '142\n' +
            '0.0\n' +
            '143\n' +
            '0.03937007874016\n' +
            '144\n' +
            '1.0\n' +
            '145\n' +
            '0.0\n' +
            '146\n' +
            '1.0\n' +
            '147\n' +
            '0.625\n' +
            ' 71\n' +
            '     0\n' +
            ' 72\n' +
            '     0\n' +
            ' 73\n' +
            '     0\n' +
            ' 74\n' +
            '     0\n' +
            ' 75\n' +
            '     0\n' +
            ' 76\n' +
            '     0\n' +
            ' 77\n' +
            '     1\n' +
            ' 78\n' +
            '     8\n' +
            '170\n' +
            '     0\n' +
            '171\n' +
            '     3\n' +
            '172\n' +
            '     1\n' +
            '173\n' +
            '     0\n' +
            '174\n' +
            '     0\n' +
            '175\n' +
            '     0\n' +
            '176\n' +
            '     0\n' +
            '177\n' +
            '     0\n' +
            '178\n' +
            '     0\n' +
            '  0\n' +
            'DIMSTYLE\n' +
            '  2\n' +
            'ISO-25\n' +
            ' 70\n' +
            '     0\n' +
            '  3\n' +
            '\n' +
            '  4\n' +
            '\n' +
            '  5\n' +
            '\n' +
            '  6\n' +
            '\n' +
            '  7\n' +
            '\n' +
            ' 40\n' +
            '1.0\n' +
            ' 41\n' +
            '2.5\n' +
            ' 42\n' +
            '0.625\n' +
            ' 43\n' +
            '3.75\n' +
            ' 44\n' +
            '1.25\n' +
            ' 45\n' +
            '0.0\n' +
            ' 46\n' +
            '0.0\n' +
            ' 47\n' +
            '0.0\n' +
            ' 48\n' +
            '0.0\n' +
            '140\n' +
            '2.5\n' +
            '141\n' +
            '2.5\n' +
            '142\n' +
            '0.0\n' +
            '143\n' +
            '0.03937007874016\n' +
            '144\n' +
            '1.0\n' +
            '145\n' +
            '0.0\n' +
            '146\n' +
            '1.0\n' +
            '147\n' +
            '0.625\n' +
            ' 71\n' +
            '     0\n' +
            ' 72\n' +
            '     0\n' +
            ' 73\n' +
            '     0\n' +
            ' 74\n' +
            '     0\n' +
            ' 75\n' +
            '     0\n' +
            ' 76\n' +
            '     0\n' +
            ' 77\n' +
            '     1\n' +
            ' 78\n' +
            '     8\n' +
            '170\n' +
            '     0\n' +
            '171\n' +
            '     3\n' +
            '172\n' +
            '     1\n' +
            '173\n' +
            '     0\n' +
            '174\n' +
            '     0\n' +
            '175\n' +
            '     0\n' +
            '176\n' +
            '     0\n' +
            '177\n' +
            '     0\n' +
            '178\n' +
            '     0\n' +
            '  0\n' +
            'ENDTAB\n' +
            '  0\n' +
            'ENDSEC\n' +
            '  0\n' +
            'SECTION\n' +
            '  2\n' +
            'BLOCKS\n' +
            '  0\n' +
            'BLOCK\n' +
            '  8\n' +
            '0\n' +
            '  2\n' +
            '$MODEL_SPACE\n' +
            ' 70\n' +
            '     0\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  3\n' +
            '$MODEL_SPACE\n' +
            '  1\n' +
            '\n' +
            '  0\n' +
            'ENDBLK\n' +
            '  5\n' +
            '21\n' +
            '  8\n' +
            '0\n' +
            '  0\n' +
            'BLOCK\n' +
            ' 67\n' +
            '     1\n' +
            '  8\n' +
            '0\n' +
            '  2\n' +
            '$PAPER_SPACE\n' +
            ' 70\n' +
            '     0\n' +
            ' 10\n' +
            '0.0\n' +
            ' 20\n' +
            '0.0\n' +
            ' 30\n' +
            '0.0\n' +
            '  3\n' +
            '$PAPER_SPACE\n' +
            '  1\n' +
            '\n' +
            '  0\n' +
            'ENDBLK\n' +
            '  5\n' +
            'D5\n' +
            ' 67\n' +
            '     1\n' +
            '  8\n' +
            '0\n' +
            '  0\n' +
            'ENDSEC\n' +
            '  0\n' +
            'SECTION\n' +
            '  2\n' +
            'ENTITIES\n' +
            polygons +
            '  0\n' +
            'ENDSEC\n' +
            '  0\n' +
            'EOF\n' +
            '\n' +
            '      \n' +
            '      ';
}

