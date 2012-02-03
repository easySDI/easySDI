(: A full text search over a set of books: either of the two phrases needs to be contained in the title:)

fn:doc('books.xml')/books/book/title[. ftcontains 'Bringing Down the House: How Six Students Took Vegas for Millions' ftor 'Beat the Dealer']