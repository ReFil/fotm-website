use std::{
    io::{Read, Write},
    net::TcpListener,
    string::String,
};

use rand::seq::SliceRandom;

macro_rules! PERSON_STRING {
    () => {
        r#"<div class="person">
    <h3>{}</h3>
    <p>{}</p>
    <div class="badge_grid">
        {}
    </div>
</div>
"#
    };
}

fn main() {
    let server = "127.0.0.1";

    let data = reqwest::blocking::get(format!("http://{}/assets/people.json", server).as_str())
        .unwrap()
        .text()
        .unwrap();

    let elements: serde_json::Value = serde_json::from_str(data.as_str()).unwrap();
    println!("{} people in the list", elements.as_array().unwrap().len());

    let mut rng = rand::rng();
    format_people(&elements, &mut rng, server);
    let socket = TcpListener::bind(format!("{}:8413", server).as_str()).unwrap();
    loop {
        let (mut stream, _) = socket.accept().unwrap();
        let mut idc = [0_u8; 1024];
        stream.read(&mut idc).unwrap();
        let content = format_people(&elements, &mut rng, server);
        let message = format!("HTTP/1.1 200 Ok\r\n\r\n{}\r\n", content);
        stream.write_all(message.as_bytes()).unwrap();
    }
}

fn format_people(
    people: &serde_json::Value,
    rng: &mut rand::rngs::ThreadRng,
    server_name: &str,
) -> String {
    let num_people = people.as_array().unwrap_or(&Vec::new()).len();
    let mut available = (0..num_people).collect::<Vec<usize>>();
    available.shuffle(rng);

    let mut out = format!(
        "<head><link rel=stylesheet href=\"http://{}/style.css\"></head><div class=\"people\">\n",
        server_name
    );
    for person_index in available {
        let person = people.get(person_index).unwrap();
        let mut badge_string = String::from("<div class=\"badge-grid\"> \n");
        let all_badges = person.get("badges").unwrap().as_array().unwrap();
        // TODO refactor to make this simpler
        for badge in all_badges {
            badge_string.push_str("<a href=\"");
            badge_string.push_str(badge.get("link").unwrap().as_str().unwrap());
            badge_string.push_str("\">\n<img src = \"");
            let asset_name = badge.get("asset").unwrap().as_str().unwrap();
            let expanded_asset_name = asset_name.replace("$server", server_name);
            badge_string.push_str(expanded_asset_name.as_str());
            badge_string.push_str("\">\n</a>");
        }
        badge_string.push_str("</div>");
        let name = person.get("name").unwrap().as_str().unwrap();
        let description = person.get("description").unwrap().as_str().unwrap();

        let output_string = format!(PERSON_STRING!(), name, description, badge_string);

        out.push_str(output_string.as_str());
    }
    out.push_str("</div>");
    out
}
