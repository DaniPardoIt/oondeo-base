
const removeAccents = (str) => {
  return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
};

async function get_async_posts( args={} ){
	// get_async_posts({
  //   post_type: "lp_course",
  // });
	
	console.log("args", args);

	let strArgs = tostring_obj_entries( args );

  const response = await fetch(
  	`/wp-admin/admin-ajax.php`,
  	{
  		method : "POST",
  		body  : new URLSearchParams({
  			action: 'get_async_posts',
				args: strArgs
  		}).toString(),
  		headers:{
  			'Content-Type': 'application/x-www-form-urlencoded'
  		}
  	}
  );
	
  const posts = await response.json();
  console.log("ASYNC POSTS", posts);

  return posts;
}

async function get_async_unique_post( args={'ID':0} ){
	// get_async_unique_post({
  //   ID: 4818,
  //   post_type: "lp_course",
  // });

	let strArgs = tostring_obj_entries( args );
	console.log('get_async_post', {
		'args': args,
		'strArgs': strArgs
	})

  const response = await fetch(`/wp-admin/admin-ajax.php`, {
    method: "POST",
    body: new URLSearchParams({
      action: "get_async_unique_post",
      args: strArgs,
    }).toString(),
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  });
	
  const post = await response.json();
  console.log("ASYNC POST", post);

  return post;
}

function tostring_obj_entries( obj ){
	let arrObj = [];
  Object.entries(obj).forEach(([key, value]) => {
    console.log(key, value);
    arrObj.push(key + "=" + value);
  });
  console.log("arrObj", arrObj);

	let strObj = arrObj.join("||");
	console.log("strObj", strObj);

	return strObj;
}



///* FETCH POST */
// const response = await fetch(
// 	`/wp-admin/admin-ajax.php`,
// 	{
// 		method : "POST",
// 		body  : new URLSearchParams({
// 			action: 'moderar_comentario',
// 			post_id: post_id,
// 			comment_id: comment_id,
// 			status: status
// 		}).toString(),
// 		headers:{
// 			'Content-Type': 'application/x-www-form-urlencoded'
// 		}
// 	}
// );