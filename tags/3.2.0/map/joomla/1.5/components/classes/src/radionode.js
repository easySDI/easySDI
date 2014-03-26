Ext.tree.RadioNode = function() {
    Ext.tree.RadioNode.superclass.constructor.apply(this, arguments);
};

Ext.extend(Ext.tree.RadioNode, Ext.tree.TreeNodeUI, {

  render : function(bulkRender){
        var n = this.node, a = n.attributes;
        var targetNode = n.parentNode ?
              n.parentNode.ui.getContainer() : n.ownerTree.innerCt.dom;

        if(!this.rendered){
            this.rendered = true;

            this.renderElements(n, a, targetNode, bulkRender);

            if(a.qtip){
               if(this.textNode.setAttributeNS){
                   this.textNode.setAttributeNS("ext", "qtip", a.qtip);
                   if(a.qtipTitle){
                       this.textNode.setAttributeNS("ext", "qtitle", a.qtipTitle);
                   }
               }else{
                   this.textNode.setAttribute("ext:qtip", a.qtip);
                   if(a.qtipTitle){
                       this.textNode.setAttribute("ext:qtitle", a.qtipTitle);
                   }
               }
            }else if(a.qtipCfg){
                a.qtipCfg.target = Ext.id(this.textNode);
                Ext.QuickTips.register(a.qtipCfg);
            }

            this.initEvents();

            if(!this.node.expanded){
                this.updateExpandIcon(true);
            }
        }else{
            if(bulkRender === true) {
                targetNode.appendChild(this.wrap);
            }
        }
    },


    renderElements : function(n, a, targetNode, bulkRender){

        this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

        var cb = typeof a.checked == 'boolean';

        var href = a.href ? a.href : Ext.isGecko ? "" : "#";

        //if(!(a.removeInput || n.removeInput)) {
        var ic = {};
        ic.tree = n.getOwnerTree();
        ic.inputName = ic.tree.inputName || a.inputName || n.inputName || ic.tree.id + '-input';
        ic.checked = a.checked || n.checked || '';
        ic.value = a.inputValue || n.inputValue || (ic.checked?'on':'');
        ic.id = Ext.id();

        var buf = ['<li class="x-tree-node"><div ext:tree-node-id="',n.id,'" class="x-tree-node-el x-tree-node-leaf x-unselectable ', a.cls,'" unselectable="on">',
            '<span class="x-tree-node-indent">',this.indentMarkup,"</span>",
            '<img src="', this.emptyIcon, '" class="x-tree-ec-icon x-tree-elbow" />',
            '<img src="', a.icon || this.emptyIcon, '" class="x-tree-node-icon',(a.icon ? " x-tree-node-inline-icon" : ""),(a.iconCls ? " "+a.iconCls : ""),'" unselectable="on" />',
            cb ? ('<input class="x-tree-node-cb" id="' + ic.id + '" type="radio" name="'+ ic.inputName + '" value="' + ic.value + '"' + (a.checked ? 'checked="checked" />' : '/>')) : '',
            '<a hidefocus="on" class="x-tree-node-anchor" href="',href,'" tabIndex="1" ',
             a.hrefTarget ? ' target="' + a.hrefTarget+'"' : "", '><span unselectable="on">',n.text,"</span></a></div>",
            '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>"].join('');

        var nel;
        if(bulkRender !== true && n.nextSibling && (nel = n.nextSibling.ui.getEl())){
            this.wrap = Ext.DomHelper.insertHtml("beforeBegin", nel, buf);
        }else{
            this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf);
        }

        this.elNode = this.wrap.childNodes[0];
        this.ctNode = this.wrap.childNodes[1];
        var cs = this.elNode.childNodes;
        this.indentNode = cs[0];
        this.ecNode = cs[1];
        this.iconNode = cs[2];
        var index = 3;
        if(cb) {
          this.checkbox = cs[3];
      //this.checkbox.defaultChecked = this.checkbox.checked;
            //index++;
        }
        this.anchor = cs[index];
        this.textNode = cs[index].firstChild;
    },

    onClick: function(e) {
      if(this.dropping){
        e.stopEvent();
        return;
      }
      if(this.fireEvent("beforeclick", this.node, e) !== false){
        var a = e.getTarget('a');
        if(!this.disabled && this.node.attributes.href && a){
          this.fireEvent("click", this.node, e);
         return;
        }else if(a && e.ctrlKey){
          e.stopEvent();
        }
				if (this.checkbox	&& !this.checkbox.checked) e.preventDefault();
        if(this.disabled){
          return;
        }
        if(this.checkbox){
			    this.toggleCheck(true);
        }
        if(this.node.attributes.singleClickExpand && !this.animating && this.node.isExpandable()){
          this.node.toggle();
        }

        this.fireEvent("click", this.node, e);
      }else{
        e.stopEvent();
      }
    }

});