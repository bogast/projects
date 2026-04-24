data() {
age: 25,
}
data() {
    return {
    text: 'Vue',
    }
}
methods: {
    show: function() {
    alert(this.text);
    }
}
methods: {
    show: function(str) {
    alert(str);
    }
}