if (document.querySelectorAll('.single-team')){

	let identity = document.getElementById('identity');
	let table = document.getElementById('table-holder');
	let gform_scores = document.getElementById('input_1_2');


	identity.addEventListener("change", idSet);


	function idSet(){
		let user = document.getElementById(identity.value);
		let parent = document.getElementById(identity.value).parentNode;
		let name = user.innerHTML;
		console.log(name)
		console.log(name.length)
		if (document.querySelectorAll(".self")[0]){
			let oldSelf = document.querySelectorAll(".self")[0];
			let oldName = document.querySelectorAll(".self")[0].firstChild;
			let end = (oldName.innerHTML.length)-6;
			//oldName.innerHTML.slice(0,end)
		    oldName.innerHTML = oldName.innerHTML.slice(0,end);//remove self if name changes
	   	 	oldSelf.classList.remove("self");
		}

		user.innerHTML = user.innerHTML + ' (you)';
		parent.classList.add('self');
		document.getElementById('input_1_1').value = identity.value;//set gravity form student value
	}
	let scores = document.querySelectorAll('#team-rubric-table select')

	scores.forEach((score) => {
	  score.addEventListener('change', () => {
	  	let assignment = document.getElementById('input_1_5').value;
	    gform_scores.value =  mergeElements(teamMembers(), scoreKeeper(scores), assignment );//write all the scores in
	  });
	});


	function scoreKeeper(scores){
		let allScores = [];
		scores.forEach((score) => {
		  allScores.push(score.value)
		});
		return allScores;
	}

	function teamMembers(){
		allMembers = [];
		let members = document.querySelectorAll('tr td:first-child');
		members.forEach((member) => {
		  allMembers.push(member.innerHTML)
		});
		return allMembers;
	}

  function mergeElements(members, scores, assignment){
    let json = [];
    let count = 0;
    members.forEach(function(memb){
      let stu = {};
      console.log(memb);
      stu['assignment'] = assignment;
      stu['student'] = memb;
      stu['scores'] = scores.slice(count, count+5);
      console.log(count)
      count = count + 5;
      json.push(stu);
    })
    console.log(json)
    return JSON.stringify(json);
  }
  
	//#input_1_1 - student name
	//#input_1_2 - assessment


	jQuery(document).bind('gform_confirmation_loaded', function(event, formId){
	    // code to be trigger when confirmation page is loaded
	   console.log('form ok and submitted')
	   document.getElementById('rubric').classList.add('hidden');
	});
}

//SCRIPT TO BUILD THE TABLES AND FILL THEM WITH DATA
data.forEach(function(item){
  let theAssignment = item.assignment;
  let cleanAssignment = cleanWords(theAssignment);
  addContent(cleanAssignment, item.student, item.scores)
})

function addContent(assignment, student, stats){
  let holder = document.getElementById(assignment)
  let destination = holder.getElementsByClassName(cleanWords(student)+'-data')[0];
  let row = destination.insertRow();
  row.innerHTML = '<td>' +student + '</td>'+ cleanStats(stats);

}


function cleanWords(words){
 if(words.includes(' ')){
   	words = words.split(' ').join('-');
	}
  if (words.includes("-(you)")){
      words = words.substr(0, (words.length-6))
      }
  return words.toLowerCase();
}

function cleanStats(stats){
  let clean = '';
  stats.forEach(function(record){
   clean += '<td>' + record + '</td>'
  })
  return clean;
}