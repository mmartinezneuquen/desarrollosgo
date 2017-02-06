var PradoCheckboxList = function(id, idValues = "", checkFunction = "") 
{	
	//console.log("<");//console.log(this);//console.log(">");
	this.checks = jQuery("input[type=checkbox][id*="+id+"]");
	this.labels = jQuery("label[for*="+id+"]");
	//this.valores = ""; // prácticamente no tiene sentido esta variable
	this.valoresField = idValues ? jQuery("input[type=hidden][id*="+idValues+"]") : false;
	this.all = [];

	this.selected = function() {
		return this.all.filter(function(elem) {
			return elem.checked();
		}.bind(this));
	}
		
	this.check = function(idItem, value = true) {
		return this.all[idItem].check(value);
	}.bind(this);

	this.uncheck = function(idItem) {
		return this.all[idItem].uncheck();
	}.bind(this);

	this.setValues = function(valores) {
		this.all.forEach(function(elem) {
			elem.uncheck();
		});
		if (valores && valores != "0") { //>> estudiar los posibles resultados nulos
			valores.split(",").forEach(function(elem){
				this.all[elem].check();
			}.bind(this));
		}
	}.bind(this);

	this.getValues = function() {
		return this.selected().map(function(elem, i){
			return jQuery(elem.checkbox).val();
		}).join(",");
	}.bind(this);

	this.checkAction = checkFunction;
//console.log("Que pasa?");
	this.update = function() {
		if (this.valoresField) this.valoresField.val(this.getValues());
		//console.log(this.checkAction);
		return this.checkAction ? this.checkAction() : undefined;
	}.bind(this);

	// Construcción inicial:
	this.checks.each(function(i, elem) {

		jQuery(elem).change(function(elem) {
			//console.log(this.getValues());
			
			return this.update();
		}.bind(this));

		this.all[jQuery(elem).val()] = {
			name: jQuery(this.labels[i]).html(),
			checkbox: elem,
			check: function(value = true) {return this.checked = value;}.bind(elem),
			uncheck: function(){return this.checked = false;}.bind(elem),
			checked: function(){return this.checked;}.bind(elem),
		}
	}.bind(this));

	this.setValues(this.valoresField.val());

	this.update();

	return this;
} 


//>> Poder asignar la update desde el constructor para poder invocarse en dicho momento
//>> Incluir el hidden de los valores en la clase, pues es prácticamente imprescindible.