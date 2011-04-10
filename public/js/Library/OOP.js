/*
Function.prototype.extends = function(className)
{console.log('extend:'+this.prototype, className)};

Function.prototype.implements = function(className)
{console.log(this.prototype, className)};

Function.prototype.method = function(method, fn)
{
	this.prototype[method] = fn;
	return this;
};

var Interface = function()
{

};

var X = {};

var iFatih = new Interface('iFatih', ['a', 'b']);

var Fatih = (function(){ with(Fatih) { extends(X); implements(iFatih); }

	var constants = {
		NAME : 'fatih',
		SURNAME : 'akin'
	};

	this.getConstant = function()
	{
		return constants[name];
	}

})

.method('asd', function() {

});

var x = new Fatih;
x.getConstant('NAME');

module('fatih');

test('a', '2');*/
myapp = {};

myapp.Greeter = function() {
};

myapp.Greeter.prototype.greet = function(name) {
	return "Hello " + name + "!";
};

GreeterTest = TestCase("GreeterTest");

GreeterTest.prototype.testGreet = function() {
	var greeter = new myapp.Greeter();
	assertEquals("Helslo World!", greeter.greet("World"));
};