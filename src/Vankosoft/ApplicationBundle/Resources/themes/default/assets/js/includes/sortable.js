import { VsPath } from '../includes/fos_js_routes.js';

class VsSortable
{
    constructor( sortActionRoute )
    {
        this.sortActionRoute    = sortActionRoute;
    }
    
    changeOrder( itemId, itemPosition )
    {
        $.ajax({
            type: 'GET',
            url: VsPath( this.sortActionRoute, { 'id': itemId, 'position': itemPosition } ),
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    document.location   = document.location;
                } else {
                    alert( 'ERROR !!!' );
                }
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'ERROR !!!' );
            }
        });
    }
    
    computeNewPosition( itemIndex, itemsPositions )
    {
        let prevItemPosition    = ( ( itemIndex - 1 ) in itemsPositions ) ? itemsPositions[itemIndex - 1] : undefined;
        let nextItemPosition    = ( ( itemIndex + 1 ) in itemsPositions ) ? itemsPositions[itemIndex + 1] : undefined;
        
        let newPosition         = 'undefined';
        let positionStep        = 10;
        
        if ( prevItemPosition ) {
            newPosition  = prevItemPosition + positionStep;
            while ( itemsPositions.includes( newPosition ) ) {
                newPosition  = newPosition + positionStep;
            }
        } else if ( nextItemPosition ) {
            newPosition  = nextItemPosition - positionStep;
            while ( itemsPositions.includes( newPosition ) ) {
                newPosition  = newPosition - positionStep;
            }
        }
        
        return newPosition;
    }
    
    changeOrderNew( itemId, insertAfterId )
    {
        //alert( insertAfterId );
        
        $.ajax({
            type: 'GET',
            url: VsPath( this.sortActionRoute, { 'id': itemId, 'insertAfterId': insertAfterId } ),
            success: function ( data )
            {
                if ( data['status'] == 'ok' ) {
                    document.location   = document.location;
                } else {
                    alert( 'ERROR !!!' );
                }
            }, 
            error: function( XMLHttpRequest, textStatus, errorThrown )
            {
                alert( 'ERROR !!!' );
            }
        });
    }
    
    getInsertAfterId( itemIndex, sortedItems )
    {
        let prevItemId  = ( ( itemIndex - 1 ) in sortedItems ) ? sortedItems[itemIndex - 1] : undefined;
        
        return prevItemId ? prevItemId : 0;
    }
}

export default VsSortable;
