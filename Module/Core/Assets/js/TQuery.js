var TQuery = function(action){
	this.action = action;
};

TQuery.prototype.send = function(data){
    Core.showOverlay();
	$.ajax({
		url:     this.action,
		context: this,
		type:    'POST',
		data:    data,
		success: this._masterCallback
	});
};

TQuery.prototype._masterCallback = function(data){
	try {
		var result = JSON.parse(data);

		if(result.error)
			toastr.error(result.error);
		else if(result.warning) {
			toastr.warning(result.warning);
			if(result.data)
				this.callback(result.data);
		}
		else
			this.callback(result.data);

	} catch (e){
		console.log(e);
		console.log(data);
		$('body').append(data);
	}
	Core.hideOverlay();
};

TQuery.prototype.callback = function(data){
	if(typeof data == "string")
		toastr.success(data);
};

TQuery.prototype.setCallback = function(c){
	this.callback = c;
};