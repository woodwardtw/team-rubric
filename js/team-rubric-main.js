let identity = document.getElementById('identity');
let table = document.getElementById('table-holder');
let gform_scores = document.getElementById('input_1_2');


identity.addEventListener("change", idSet);


function idSet(){
	let user = document.getElementById(identity.value);
	if (document.querySelectorAll(".self")[0]){
		let oldSelf = document.querySelectorAll(".self")[0];
   	 	oldSelf.classList.remove("self");
   	 	let name = oldSelf.innerHTML;
   	 	oldSelf.innerHTML = oldSelf.innerHTML.slice(0,(name.length-7));//remove self if name changes
	}

	user.innerHTML = user.innerHTML + ' (self)';
	user.classList.add('self');
	document.getElementById('input_1_1').value = identity.value;//set gravity form student value
}

let scores = document.querySelectorAll('select')
console.log(scores)

scores.forEach((score) => {
  score.addEventListener('change', () => {
    gform_scores.value = scoreKeeper(scores);
  });
});


function scoreKeeper(scores){
	let allScores = [];
	scores.forEach((score) => {
	  console.log(score.value);
	  allScores.push(score.value)
	});
	return allScores;
}

//#input_1_1 - student name
//#input_1_2 - assessment